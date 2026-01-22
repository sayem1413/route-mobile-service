<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\RouteMobileContract;
use App\Services\Gateway\RouteMobileGateway;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RouteMobileContract::class, RouteMobileGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
