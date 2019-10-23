<?php

namespace @namespace;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use @workspace_name\UserModule\Models\PermissionScope;

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

        if(!Schema::hasTable('users')) {
            Artisan::call('migrate:refresh', ['--path' => 'vendor/@module/jp-user-module/packages/jarplay/user-module/src/Database/migrations']);
            Artisan::call('migrate:refresh', ['--path' => 'vendor/laravel/passport/database/migrations']);
            Artisan::call('passport:install');
        }

        $this->app->register(\@workspace_name\UserModule\UserModuleServiceProvider::class);
        $this->app->register(\@workspace_name\CommonLibraryModule\CommonLibraryModuleServiceProvider::class);

        $scopes = PermissionScope::select(['scope', 'name'])->where('module_id', config('@module-module.constants.MODULE_ID'))->where('status', config('@module-module.constants.STATUS.ACTIVE'))->get();

        $scopes = $scopes->mapWithKeys(function ($item) {
            return [ $item->scope => $item->name ];
        });

        Passport::tokensCan($scopes->all());

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
