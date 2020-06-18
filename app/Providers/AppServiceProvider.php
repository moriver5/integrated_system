<?php

namespace App\Providers;

use App\VendorOverrides\DB\CustomDatabaseManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('db', function ($app) {
            return new CustomDatabaseManager($app, $app['db.factory']);
        });
    }
}
