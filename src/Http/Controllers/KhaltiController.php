<?php

namespace Subash\Khalti\Http\Controllers;

use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Services\Traits\PaymentTrait;
use OrderHelper;
use Illuminate\Http\Request;
use Throwable;

class KhaltiController extends BaseController
{
    use PaymentTrait;

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function paymentSuccess(Request $request, BaseHttpResponse $response)
    {
        $amountToBePaid = $request->input('amount');

        $data = http_build_query(array(
            'token' => $request->input('token'),
            'amount'  => $amountToBePaid //in paisa
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, KHALTI_VERIFICATION_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = ['Authorization: Key ' . get_payment_setting('secret', KHALTI_PAYMENT_METHOD_NAME)];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $decoded_response = json_decode($result, true);

        $status = PaymentStatusEnum::PENDING;
        $verified = false;
        if ($status_code == 200 && isset($decoded_response['idx'])) {
            //payment verified
            $status = PaymentStatusEnum::COMPLETED;
            $verified = true;
        } else {
            if (isset($decoded_response['error_key'])) {
                switch ($decoded_response['error_key']) {
                    case 'already_verified':
                        //means already purchased | do nothing
                        $status = PaymentStatusEnum::COMPLETED;
                        $verified = true;
                        break;
                    case 'validation_error':
                        //not validated
                        $status = PaymentStatusEnum::FRAUD;
                        break;
                    default:
                        //could not be verified
                        $status = PaymentStatusEnum::FRAUD;
                        break;
                }
            } else {
                //could not be verified
                $status = PaymentStatusEnum::FAILED;
            }
        }
        $this->storeLocalPayment([
            'amount'          => ($amountToBePaid / 100),
            'currency'        => $request->input('currency'),
            'charge_id'       => $request->input('token'),
            'payment_channel' => KHALTI_PAYMENT_METHOD_NAME,
            'status'          => $status,
            'customer_id'     => auth('customer')->check() ? auth('customer')->user()->getAuthIdentifier() : null,
            'payment_type'    => 'direct',
            'order_id'        => $request->input('order_id'),
        ]);

        OrderHelper::processOrder($request->input('order_id'), $request->input('token'));
        if (!$verified) {
            return $response
                ->setError()
                ->setNextUrl(route('public.checkout.success', OrderHelper::getOrderSessionToken()))
                ->setMessage(__('Order placed. However, payment was not verified. Please contact our administration!'));
        }

        return $response
            ->setNextUrl(route('public.checkout.success', OrderHelper::getOrderSessionToken()))
            ->setMessage(__('Checkout Successful!'));
    }

    public function paymentFailure(Request $request, BaseHttpResponse $response){
        return $response
        ->setError()
        ->setNextUrl(route('public.checkout.information', OrderHelper::getOrderSessionToken()))
        ->setMessage(__('Khalti Payment Failed !'));
    }
}
