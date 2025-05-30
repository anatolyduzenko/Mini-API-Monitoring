<?php

namespace App\Providers;

use App\Listeners\Monitoring\Interfaces\MonitoringHandlerInterface;
use App\Listeners\Monitoring\ResponseTimeChangeHandler;
use App\Listeners\Monitoring\StatusChangeHandler;
use App\Services\MonitoringDispatcherService;
use Illuminate\Support\ServiceProvider;

class BroadcastMonitoringProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->tag([
            StatusChangeHandler::class,
            ResponseTimeChangeHandler::class,
        ], 'monitoring.handlers');

        $this->app->singleton(MonitoringDispatcherService::class, function ($app) {
            $handlers = $app->tagged('monitoring.handlers');

            return new MonitoringDispatcherService($handlers);
        });

        $this->app->alias(MonitoringDispatcherService::class, MonitoringHandlerInterface::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
