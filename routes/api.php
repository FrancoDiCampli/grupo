<?php

use Illuminate\Http\Request;

Route::middleware('auth:airlock')->group(function () {

    /*Auth*/
    Route::get('/user', 'API\UsersController@user');
    Route::post('/update_account', 'API\UsersController@updateAccount');

    /*Notificaciones*/
    Route::get('notifications', 'API\NotificationsController@getNotifications');

    /*Ventas*/
    Route::apiResource('ventas', 'API\VentasController', ['except' => ['create', 'edit']]);

    /*Presupuestos*/
    Route::apiResource('presupuestos', 'API\PresupuestosController', ['except' => ['create', 'edit']]);

    /*Clientes*/
    Route::apiResource('clientes', 'API\ClientesController', ['except' => ['create', 'edit']]);

    /*CuentasCorrientes*/
    Route::post('/pagarcuentas', 'API\CuentacorrientesController@pagar');
    Route::post('/recargar', 'API\CuentacorrientesController@recargar');

    /*Articulos*/
    Route::apiResource('articulos', 'API\ArticulosController', ['except' => ['create', 'edit']]);

    /*Categorias*/
    Route::apiResource('categorias', 'API\CategoriasController', ['only' => ['index']]);

    /*Marcas*/
    Route::apiResource('marcas', 'API\MarcasController', ['only' => ['index']]);

    /*Inventarios*/
    Route::apiResource('inventarios', 'API\InventariosController', ['only' => ['index', 'store']]);

    /*Proveedores*/
    Route::apiResource('suppliers', 'API\SuppliersController',  ['except' => ['create', 'edit']]);

    /*Remitos*/
    Route::apiResource('compras', 'API\ComprasController');

    // Movimientos
    Route::apiResource('movimientos', API\MovimientosController::class, ['only' => ['index']]);

    /*PDFs*/
    Route::get('presupuestosPDF/{id}', 'API\PdfController@presupuestosPDF');
    Route::get('ventasPDF/{id}', 'API\PdfController@ventasPDF');
    Route::get('facturasPDF/{id}', 'API\PdfController@facturasPDF');
    Route::get('comprasPDF/{id}', 'API\PdfController@comprasPDF');
    Route::get('recibosPDF/{id}', 'API\PdfController@recibosPDF');

    // Reportes
    Route::get('estadisticas/ventas', 'API\EstadisticasController@ventas');
    Route::get('estadisticas/detallesVentas', 'API\EstadisticasController@detallesVentas');
    Route::get('estadisticas/detallesCompras', 'API\EstadisticasController@detallesCompras');
    Route::get('estadisticas/compras', 'API\EstadisticasController@compras');
    Route::get('cartera', 'API\EstadisticasController@cartera');

    // Mi Cuenta
    Route::get('mi_cuenta', 'API\ClientesController@miCuenta');
    Route::get('showRecibo/{id}', 'API\ClientesController@showRecibo');

    // Dolar
    Route::group(['middleware' => 'cors'], function () {
        Route::get('/consultar', 'API\DolarController@consultar');
    });

    // REVISAR
    Route::post('/formaPago', 'API\CuentacorrientesController@formaPago');
    Route::post('facturar', [VentasController::class, 'facturar']);
    Route::apiResource('facturas', API\FacturasController::class, ['only' => ['index', 'store', 'show']]);
    Route::post('/buscando', 'API\BuscadorController@buscando');
    // ______________________________________________________________

    /*Negocios*/
    Route::apiResource('negocios', 'API\NegociosController', ['except' => ['create', 'edit']]);

    /*Roles & Permissions*/
    Route::apiResource('roles', 'API\RolesController', ['except' => ['create', 'edit', 'show']]);
    Route::get('permissions', 'API\RolesController@permissions');

    /*Users*/
    Route::apiResource('users', 'API\UsersController', ['except' => ['create', 'edit', 'show']]);

    /*Configuraciones*/
    Route::get('/config', 'API\PreferencesController@getConfig');
});
