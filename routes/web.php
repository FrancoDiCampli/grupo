<?php

use App\Permission;

Route::get('/', function () {
    return view('welcome');
});

// Vue Capture
Route::get('/{vue_capture?}', function () {
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*');

// Auth Routes
Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('permissions', function () {
    return Permission::toString();
});
