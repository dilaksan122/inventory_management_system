<?php

namespace App\Providers;

use App\Models\SystemSettings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\Payments\PaypalService;
use App\Services\Payments\StripeService;
use App\Services\Product\ProductService;
use App\Services\Utils\FileUploadService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FileUploadService::class, function ($app) {
            return new FileUploadService();
        });

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService();
        });

        $this->app->singleton(StripeService::class, function ($app) {
            return new StripeService();
        });
        $this->app->singleton(PaypalService::class, function ($app) {
            return new PaypalService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            Schema::defaultStringLength(191);
            $settings = SystemSettings::all();
            $settings_array = convertDbSettingsToConfig($settings);
            Config::set($settings_array);

            Config::set([
                // Stripe
                'stripe.currency_code' => 'USD',
                // Paypal
                'paypal.currency_code' => 'USD',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
