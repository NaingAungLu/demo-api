<?php

use @workspace_name\UserModule\Middleware\CheckToken;

Route::group(['middleware' => \Barryvdh\Cors\HandleCors::class, 'prefix' => 'api/v1/@module'], function() {

    Route::get('/health-check', function() {
        return 'OK';
    });

    Route::get('/version', function() {
        return Config::get('@module.constants.API_VERSION');
    });

    Route::namespace('@namespace\Controllers')->group(function() {

        #EndLine
    });
});