<?php

namespace App\Providers;

use App\Delivery\DeliveryManager;
use App\Services\GoPayService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GoPayService::class);
        $this->app->singleton(DeliveryManager::class, fn () => new DeliveryManager(config('delivery.drivers', [])));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
