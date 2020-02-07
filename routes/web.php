<?php

use App\Permission;
use App\Http\Controllers\API\VentasController;
use App\Http\Controllers\API\BuscadorController;
use App\Http\Controllers\API\FacturasController;
use App\Http\Controllers\API\NegociosController;
use App\Http\Controllers\API\MovimientosController;

Route::get('/', function () {
    return view('welcome');
});

// Vue Capture
Route::get('/{vue_capture?}', function () {
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*');

// Auth Routes
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
