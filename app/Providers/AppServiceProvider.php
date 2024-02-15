<?php

namespace App\Providers;

use App\Services\PaymentService;
use App\Services\PrintfulService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PrintfulService::class, function () {
            return new PrintfulService();
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(config('services.stripe.secret'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
