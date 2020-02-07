<?php

use Illuminate\Http\Request;

Route::middleware('auth:airlock')->group(function () {

    /*Auth*/
    Route::get('/user', 'API\UsersController@user');
    Route::post('/update_account', 'API\UsersController@updateAccount');

    /*Negocios*/
    Route::apiResource('negocios', 'API\NegociosController', ['except' => ['create', 'edit']]);

    /*Roles & Permissions*/
    Route::apiResource('roles', 'API\RolesController', ['except' => ['create', 'edit', 'show']]);
    Route::get('permissions', 'API\RolesController@permissions');

    /*Users*/
    Route::apiResource('users', 'API\UsersController', ['except' => ['create', 'edit', 'show']]);
});
