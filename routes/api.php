<?php

use Illuminate\Http\Request;

Route::middleware('auth:airlock')->group(function () {

    // Auth
    Route::get('/user', 'API\UsersController@user');
    Route::post('/update_account', 'API\UsersController@updateAccount');
});
