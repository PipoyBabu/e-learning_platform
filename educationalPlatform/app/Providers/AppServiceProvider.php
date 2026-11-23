<?php

namespace App\Providers;

use App\Services\Payments\PaymentGateway;
use App\Services\Payments\SimulatedGateway;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, SimulatedGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (app()->environment('production')) {
            $url->forceScheme('https');
        }
    }
}
