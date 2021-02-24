@php $khaltiStatus = get_payment_setting('status', KHALTI_PAYMENT_METHOD_NAME); @endphp
<table class="table payment-method-item">
    <tbody>
    <tr class="border-pay-row">
        <td class="border-pay-col"><i class="fa fa-theme-payments"></i></td>
        <td style="width: 20%;">
            <img class="filter-black" src="{{ url('vendor/core/plugins/khalti/images/khalti.png') }}"
                 alt="Khalti">
        </td>
        <td class="border-right">
            <ul>
                <li>
                    <a href="https://khalti.com.np" target="_blank">{{ __('Khalti') }}</a>
                    <p>{{ __('Customer can buy product and pay directly via :name', ['name' => 'Khalti']) }}</p>
                </li>
            </ul>
        </td>
    </tr>
    </tbody>
    <tbody class="border-none-t">
    <tr class="bg-white">
        <td colspan="3">
            <div class="float-left" style="margin-top: 5px;">
                <div
                    class="payment-name-label-group @if (get_payment_setting('status', KHALTI_PAYMENT_METHOD_NAME) == 0) hidden @endif">
                    <span class="payment-note v-a-t">{{ trans('plugins/payment::payment.use') }}:</span> <label
                        class="ws-nm inline-display method-name-label">{{ get_payment_setting('name', KHALTI_PAYMENT_METHOD_NAME) }}</label>
                </div>
            </div>
            <div class="float-right">
                <a class="btn btn-secondary toggle-payment-item edit-payment-item-btn-trigger @if ($khaltiStatus == 0) hidden @endif">{{ trans('plugins/payment::payment.edit') }}</a>
                <a class="btn btn-secondary toggle-payment-item save-payment-item-btn-trigger @if ($khaltiStatus == 1) hidden @endif">{{ trans('plugins/payment::payment.settings') }}</a>
            </div>
        </td>
    </tr>
    <tr class="paypal-online-payment payment-content-item hidden">
        <td class="border-left" colspan="3">
            {!! Form::open() !!}
            {!! Form::hidden('type', KHALTI_PAYMENT_METHOD_NAME, ['class' => 'payment_type']) !!}
            <div class="row">
                <div class="col-sm-6">
                    <ul>
                        <li>
                            <label>{{ trans('plugins/payment::payment.configuration_instruction', ['name' => 'Khalti']) }}</label>
                        </li>
                        <li class="payment-note">
                            <p>{{ trans('plugins/payment::payment.configuration_requirement', ['name' => 'Khalti']) }}
                                :</p>
                            <ul class="m-md-l" style="list-style-type:decimal">
                                <li style="list-style-type:decimal">
                                    <a href="https://khalti.com.np" target="_blank">
                                        {{ __('Register a merchant account on :name', ['name' => 'Khalti']) }}
                                    </a>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{{ __('After registration at :name, you will have public and secret key', ['name' => 'Khalti']) }}</p>
                                </li>
                                <li style="list-style-type:decimal">
                                    <p>{{ __('Enter public, secret key into the box in right hand') }}</p>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <div class="well bg-white">
                        <div class="form-group">
                            <label class="text-title-field"
                                   for="khalti_name">{{ trans('plugins/payment::payment.method_name') }}</label>
                            <input type="text" class="next-input" name="payment_{{ KHALTI_PAYMENT_METHOD_NAME }}_name"
                                   id="khalti_name" data-counter="400"
                                   value="{{ get_payment_setting('name', KHALTI_PAYMENT_METHOD_NAME, __('Online payment via :name', ['name' => 'Khalti'])) }}">
                        </div>
                        <p class="payment-note">
                            {{ trans('plugins/payment::payment.please_provide_information') }} <a target="_blank" href="https://khalti.com.np/">Khalti</a>:
                        </p>
                        <div class="form-group">
                            <label class="text-title-field" for="{{ KHALTI_PAYMENT_METHOD_NAME }}_public">{{ __('Public Key') }}</label>
                            <input type="text" class="next-input"
                                   name="payment_{{ KHALTI_PAYMENT_METHOD_NAME }}_public" id="{{ KHALTI_PAYMENT_METHOD_NAME }}_public"
                                   value="{{ get_payment_setting('public', KHALTI_PAYMENT_METHOD_NAME) }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="{{KHALTI_PAYMENT_METHOD_NAME }}_secret">{{ __('Secret Key') }}</label>
                            <input type="password" class="next-input" placeholder="" id="{{ KHALTI_PAYMENT_METHOD_NAME }}_secret"
                                   name="payment_{{ KHALTI_PAYMENT_METHOD_NAME }}_secret"
                                   value="{{ get_payment_setting('secret', KHALTI_PAYMENT_METHOD_NAME) }}">
                        </div>
                        <div class="form-group">
                            <label class="text-title-field" for="{{ KHALTI_PAYMENT_METHOD_NAME }}_merchant_email">{{ __('Merchant Email') }}</label>
                            <input type="email" class="next-input" placeholder="{{ __('Email') }}" id="{{ KHALTI_PAYMENT_METHOD_NAME }}_merchant_email"
                                   name="payment_{{ KHALTI_PAYMENT_METHOD_NAME }}_merchant_email"
                                   value="{{ get_payment_setting('merchant_email', KHALTI_PAYMENT_METHOD_NAME) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 bg-white text-right">
                <button class="btn btn-warning disable-payment-item @if ($khaltiStatus == 0) hidden @endif"
                        type="button">{{ trans('plugins/payment::payment.deactivate') }}</button>
                <button
                    class="btn btn-info save-payment-item btn-text-trigger-save @if ($khaltiStatus == 1) hidden @endif"
                    type="button">{{ trans('plugins/payment::payment.activate') }}</button>
                <button
                    class="btn btn-info save-payment-item btn-text-trigger-update @if ($khaltiStatus == 0) hidden @endif"
                    type="button">{{ trans('plugins/payment::payment.update') }}</button>
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    </tbody>
</table>
