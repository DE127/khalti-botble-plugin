<?php

namespace Subash\Khalti\Providers;

use Botble\Ecommerce\Repositories\Interfaces\OrderAddressInterface;
use Botble\Payment\Enums\PaymentMethodEnum;
use Html;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Throwable;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerKhaltiPayment'], 16, 2);
        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithKhalti'], 16, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 97, 1);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['KHALTI'] = KHALTI_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 21, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == KHALTI_PAYMENT_METHOD_NAME) {
                $value = 'Khalti';
            }

            return $value;
        }, 21, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == KHALTI_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 21, 2);
    }

    /**
     * @param string $settings
     * @return string
     * @throws Throwable
     */
    public function addPaymentSettings($settings)
    {
        return $settings . view('plugins/khalti::settings')->render();
    }

    /**
     * @param string $html
     * @param array $data
     * @return string
     */
    public function registerKhaltiPayment($html, array $data)
    {
        return $html . view('plugins/khalti::methods', $data)->render();
    }

    /**
     * @param Request $request
     * @param array $data
     */
    public function checkoutWithKhalti($data, Request $request)
    {
        if ($request->input('payment_method') == KHALTI_PAYMENT_METHOD_NAME) {
            $orderAddress = $this->app->make(OrderAddressInterface::class)->getFirstBy(['order_id' => $request->input('order_id')]);
?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Payment Via Khalti</title>
            </head>

            <body>
                <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
                <script src="https://khalti.s3.ap-south-1.amazonaws.com/KPG/dist/2020.12.17.0.0.0/khalti-checkout.iffe.js"></script>

                <script>
                    function url_redirect(options) {
                        var $form = $("<form />");
                        $form.attr("action", options.url);
                        $form.attr("method", options.method);
                        for (var data in options.data)
                            $form.append('<input type="hidden" name="' + data + '" value="' + options.data[data] + '" />');
                        $("body").append($form);
                        $form.submit();
                    }
                    let config = {
                        "publicKey": "<?= get_payment_setting('public', KHALTI_PAYMENT_METHOD_NAME) ?>",
                        "productIdentity": "<?= $request->input('checkout-token'); ?>",
                        "productName": "<?= $request->input('name') . 'Product Name'; ?>",
                        "productUrl": "<?= url('/'); ?>",
                        "eventHandler": {
                            onSuccess(payload) {
                                payload['order_id'] = "<?= $request->input('order_id'); ?>";
                                payload['currency'] = "<?= $request->input('currency'); ?>";
                                url_redirect({
                                    url: "<?= route('khalti.payment.success'); ?>",
                                    method: "GET",
                                    data: payload,
                                });
                            },
                            onError(error) {
                                url_redirect({
                                    url: "<?= route('khalti.payment.failure'); ?>",
                                    method: "GET",
                                    data: error
                                });
                            },
                            onClose() {
                                console.log('khalti payment dialog is closing');
                                url_redirect({
                                    url: "<?= route('khalti.payment.failure'); ?>",
                                    method: "GET",
                                });
                            }
                        },
                        "paymentPreference": ["KHALTI", "EBANKING", "MOBILE_BANKING", "CONNECT_IPS", "SCT"],
                    };
                    let checkout = new KhaltiCheckout(config);
                    checkout.show({
                        amount: (parseFloat(<?= $request->input('amount'); ?>).toFixed(2) * 100)
                    });
                </script>
            </body>

            </html>

<?php
            exit;
        }
        //return $data;
    }
}
