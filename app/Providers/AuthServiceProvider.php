<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// use App\Models\PermissionScope;

use Schema;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // if(Schema::hasTable('permission_scopes')) {

        //     $scopes = PermissionScope::select(['scope', 'scope_name'])->where('status', config('constants.STATUS.ACTIVE'))->get();

        //     $scopes = $scopes->mapWithKeys(function ($item) {
        //         return [$item->scope => $item->name];
        //     });

        //     Passport::$scopes = array_merge(Passport::$scopes, $scopes->all());
        // }

        Passport::routes();
        
        Passport::tokensExpireIn(now()->addDays(15));

        Passport::refreshTokensExpireIn(now()->addDays(30));
    }
}
