<?php

namespace @namespace;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;

use Artisan;
use Schema;

class @module_nameServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['router']->aliasMiddleware('scopes', \Laravel\Passport\Http\Middleware\CheckScopes::class);
        $this->app['router']->aliasMiddleware('scope', \Laravel\Passport\Http\Middleware\CheckForAnyScope::class);

        Passport::tokensCan(["*"]);

        Passport::routes();

        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', '@module');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', '@module');
        $this->publishes([
            __DIR__ . '/Resources/lang' => resource_path('lang/vendor/@module'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/@module.constants.php', '@module.constants');
    }
}
