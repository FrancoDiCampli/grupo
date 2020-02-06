<?php

use Illuminate\Http\Request;

Route::middleware('auth:airlock')->group(function () {

    Route::get('/user', 'API\UsersController@user');
});
