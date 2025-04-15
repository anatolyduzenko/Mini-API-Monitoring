<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('testing') && config('database.connections.mariadb.database') === 'apimonitoring') {
            throw new \Exception('⚠️ Refusing to run tests on the production database!');
        }
    }
}
