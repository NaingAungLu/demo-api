<?php

namespace App\Providers;

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
        $this->app->bind(
            'App\Repositories\PackageRepositoryInterface',
            'App\Repositories\PackageRepository'
        );

        $this->app->bind(
            'App\Repositories\PromotionRepositoryInterface',
            'App\Repositories\PromotionRepository'
        );

        $this->app->bind(
            'App\Repositories\OrderRepositoryInterface',
            'App\Repositories\OrderRepository'
        );
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
