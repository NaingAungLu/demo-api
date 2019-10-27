<?php

use App\Http\Middleware\CheckToken;

Route::group(['middleware' => \Barryvdh\Cors\HandleCors::class, 'prefix' => 'v1'], function() {

	Route::get('/health-check', function() {
    	return 'OK';
    });

    Route::get('/version', function() {
    	return Config::get('constants.API_VERSION');
    });

    Route::prefix('auth')->group(function () {
    	Route::post('register', 'AuthController@register');
        
        Route::post('login', 'AuthController@login');
	});

    Route::middleware([CheckToken::class])->group(function () {
        Route::post('auth/logout', 'AuthController@logout');
    });

	Route::middleware([CheckToken::class])->group(function () {
		Route::middleware(['scope:package.read'])->get('/package', 'PackageController@index');

		Route::middleware(['scope:package.read'])->get('/package/{id}', 'PackageController@show');
	});

	Route::middleware([CheckToken::class])->group(function () {
		Route::middleware(['scope:promotion.read'])->get('/promotion', 'PromotionController@index');

		Route::middleware(['scope:promotion.read'])->get('/promotion/{id}', 'PromotionController@show');
	});

	Route::middleware([CheckToken::class])->group(function () {
		Route::middleware(['scope:order.read'])->get('/order', 'OrderController@index');

		Route::middleware(['scope:order.read'])->get('/order/{id}', 'OrderController@show');

		Route::middleware(['scope:order.write'])->post('/order', 'OrderController@store');
	});

	#EndLine
});
