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
		Route::middleware(['scope:product.read'])->get('/product', 'ProductController@index');

		Route::middleware(['scope:product.read'])->get('/product/{id}', 'ProductController@show');

		Route::middleware(['scope:product.write'])->post('/product', 'ProductController@store');

		Route::middleware(['scope:product.write'])->post('/product/{id}', 'ProductController@update');

		Route::middleware(['scope:product.remove'])->delete('/product/{id}', 'ProductController@destroy');

		Route::middleware(['scope:product.remove'])->delete('/product/{id}/permanent-delete', 'ProductController@permanentDelete');

		Route::middleware(['scope:product.write'])->post('/product/{id}/restore', 'ProductController@restore');
	});

	#EndLine
});
