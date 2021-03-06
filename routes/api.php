<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:airlock')->group(function () {

    /*Auth*/
    Route::get('/user', 'API\UsersController@user');
    Route::post('/update_account', 'API\UsersController@updateAccount');

    /*pedidos*/
    Route::apiResource('pedidos', 'API\PresupuestosController', ['except' => ['create', 'edit']]);
    Route::post('vender', 'API\PresupuestosController@vender');

    /*remitos*/
    Route::apiResource('remitos', 'API\VentasController', ['except' => ['create', 'edit']]);
    Route::post('facturar', 'API\VentasController@facturar');

    // Entregas
    Route::apiResource('entregas', 'API\EntregasController', ['except' => ['create', 'edit']]);

    /*Facturas*/
    Route::apiResource('facturas', 'API\FacturasController', ['only' => ['index', 'store', 'show', 'destroy']]);


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

    /*Compras*/
    Route::apiResource('compras', 'API\ComprasController');

    /*Consiganciones*/
    Route::apiResource('consignaciones', 'API\ConsignmentsController', ['only' => ['index', 'store', 'show']]);

    /*Devoluciones*/
    Route::apiResource('devoluciones', 'API\GiveBacksController', ['only' => ['index', 'store', 'show']]);
    Route::post('/inventariosVendedor', 'API\GiveBacksController@inventariosVendedor');

    /*Reportes*/
    Route::get('estadisticas/ventas', 'API\EstadisticasController@ventas');
    Route::get('estadisticas/detallesVentas', 'API\EstadisticasController@detallesVentas');
    Route::get('estadisticas/detallesCompras', 'API\EstadisticasController@detallesCompras');
    Route::get('estadisticas/compras', 'API\EstadisticasController@compras');
    Route::get('estadisticas/ventasClientesArticulos', 'API\EstadisticasController@ventasClientesArticulos');

    /*Movimientos*/
    Route::apiResource('movimientos', 'API\MovimientosController', ['only' => ['index']]);

    /*Cartera*/
    Route::get('cartera', 'API\EstadisticasController@cartera');
    Route::get('chequeCobrado/{id}', 'API\EstadisticasController@chequeCobrado');

    /*Users*/
    Route::apiResource('users', 'API\UsersController', ['except' => ['create', 'edit', 'show']]);

    /*Roles & Permissions*/
    Route::apiResource('roles', 'API\RolesController', ['except' => ['create', 'edit', 'show']]);
    Route::get('permissions', 'API\RolesController@permissionAll');

    /*PDFs*/
    Route::get('presupuestosPDF/{id}', 'API\PdfController@presupuestosPDF');
    Route::get('ventasPDF/{id}', 'API\PdfController@ventasPDF');
    Route::get('facturasPDF/{id}', 'API\PdfController@facturasPDF');
    Route::get('comprasPDF/{id}', 'API\PdfController@comprasPDF');
    Route::get('recibosPDF/{id}', 'API\PdfController@recibosPDF');
    Route::get('consignacionesPDF/{id}', 'API\PdfController@consignacionesPDF');
    Route::get('devolucionesPDF/{id}', 'API\PdfController@devolucionesPDF');
    Route::post('/resumenCuentaPDF', 'API\PdfController@resumenCuentaPDF');
    Route::get('entregasPDF/{id}', 'API\PdfController@entregasPDF');
    Route::get('facturasPDF/{id}', 'API\PdfController@facturasPDF');

    /* Excel */
    Route::post('reportes/ventas/export', 'API\EstadisticasController@ventasExcel');
    Route::post('reportes/articulos/export', 'API\EstadisticasController@articulosExcel');
    Route::post('reportes/compras/export', 'API\EstadisticasController@comprasExcel');
    Route::post('reportes/litrosCliente', 'API\EstadisticasController@litrosCliente');

    /*Busqueda*/
    Route::post('/buscando', 'API\BuscadorController@buscando');

    /*Notificaciones*/
    Route::apiResource('notificaciones', 'API\NotificacionesController');

    /*Configuraciones*/
    Route::get('/config', 'API\PreferencesController@getConfig');

    /*Mi Cuenta*/
    Route::get('mi_cuenta', 'API\ClientesController@miCuenta');
    Route::get('showRecibo/{id}', 'API\ClientesController@showRecibo');

    Route::post('/resumenCuenta', 'API\ClientesController@resumenCuenta');

    /*Dolar*/
    Route::get('/consultar', 'API\DolarController@consultar');
    Route::post('/setCotizacion', 'API\DolarController@setCotizacion');
});
