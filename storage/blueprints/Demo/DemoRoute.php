Route::middleware([CheckToken::class])->group(function () {
		Route::middleware(['scope:@route.read'])->get('/@route', '@model_nameController@index');

		Route::middleware(['scope:@route.read'])->get('/@route/{id}', '@model_nameController@show');

		Route::middleware(['scope:@route.write'])->post('/@route', '@model_nameController@store');

		Route::middleware(['scope:@route.write'])->post('/@route/{id}', '@model_nameController@update');

		Route::middleware(['scope:@route.remove'])->delete('/@route/{id}', '@model_nameController@destroy');

		Route::middleware(['scope:@route.remove'])->delete('/@route/{id}/permanent-delete', '@model_nameController@permanentDelete');

		Route::middleware(['scope:@route.write'])->post('/@route/{id}/restore', '@model_nameController@restore');
	});

	#EndLine