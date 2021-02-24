@if (get_payment_setting('status', KHALTI_PAYMENT_METHOD_NAME) == 1)
    <li class="list-group-item">
        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_{{ KHALTI_PAYMENT_METHOD_NAME }}"
               value="{{ KHALTI_PAYMENT_METHOD_NAME }}" data-toggle="collapse" data-target=".payment_{{ KHALTI_PAYMENT_METHOD_NAME }}_wrap"
               data-parent=".list_payment_method"
               @if (setting('default_payment_method') == KHALTI_PAYMENT_METHOD_NAME) checked @endif
        >
        <label for="payment_{{ KHALTI_PAYMENT_METHOD_NAME }}">{{ get_payment_setting('name', KHALTI_PAYMENT_METHOD_NAME) }}</label>
        <div class="payment_{{ KHALTI_PAYMENT_METHOD_NAME }}_wrap payment_collapse_wrap collapse @if (setting('default_payment_method') == KHALTI_PAYMENT_METHOD_NAME) show @endif">
            <p>{!! get_payment_setting('description', KHALTI_PAYMENT_METHOD_NAME, __('Payment with Khalti')) !!}</p>
        </div>
    </li>
@endif
