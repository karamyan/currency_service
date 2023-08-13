<?php

namespace App\Providers;

use App\Modules\Shared\CurrencyRate\Services\CurrencyRateService;
use App\Modules\Shared\CurrencyRate\Services\CurrencyRateServiceInterface;
use Illuminate\Support\ServiceProvider;

class CurrencyRateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CurrencyRateServiceInterface::class, CurrencyRateService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
