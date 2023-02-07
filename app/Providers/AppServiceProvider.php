<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaymantService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PaymantService::class, function($app) {
            return new PaymantService();
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
