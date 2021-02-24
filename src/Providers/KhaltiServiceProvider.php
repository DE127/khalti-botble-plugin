<?php

namespace Subash\Khalti\Providers;

use Botble\Base\Supports\Helper;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\ServiceProvider;

class KhaltiServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        Helper::autoload(__DIR__ . '/../../helpers');
    }

    /**
     * @throws FileNotFoundException
     */
    public function boot()
    {
        if (is_plugin_active('payment')) {
            $this->setNamespace('plugins/khalti')
                ->loadRoutes(['web'])
                ->loadAndPublishViews()
                ->publishAssets();

            $this->app->register(HookServiceProvider::class);

            $config = $this->app->make('config');

            $config->set([
                'khalti.publicKey'     => get_payment_setting('public', KHALTI_PAYMENT_METHOD_NAME),
                'khalti.secretKey'     => get_payment_setting('secret', KHALTI_PAYMENT_METHOD_NAME),
                'khalti.merchantEmail' => get_payment_setting('merchant_email', KHALTI_PAYMENT_METHOD_NAME),
                'khalti.paymentUrl'    => '',
            ]);
        }
    }
}
