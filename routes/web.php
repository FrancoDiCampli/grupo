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
Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('permissions', function () {
    return Permission::toString();
});


// Aca van las rutas??

Route::middleware('auth:api')->group(function () {
    /*TEST*/
    Route::get('/test', 'API\TestController@index');


    /*Auth*/
    Route::get('/user', 'AuthController@user');
    Route::post('/user/update', 'AuthController@update');
    Route::post('/logout', 'AuthController@logout');
    Route::post('/user/delete', 'AuthController@delete');

    /*Roles*/
    Route::apiResource('roles', 'API\RolesController', ['except' => ['create', 'edit']]);

    /*Users*/
    Route::apiResource('users', 'API\UsersController', ['except' => ['create', 'edit']]);

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

    // Negocios
    Route::apiResource('negocios', API\NegociosController::class);

    /*Configuraciones*/
    Route::get('/config', 'API\PreferencesController@getConfig');
    Route::get('/config/necesary', 'API\PreferencesController@checkNecesaryConfig');
    Route::get('/config/standard', 'API\PreferencesController@getStandardConfig');
    Route::get('/config/advance', 'API\PreferencesController@getAdvanceConfig');
    Route::post('/config/update/standard', 'API\PreferencesController@updateStandardConfig');
    Route::post('/config/update/logo', 'API\PreferencesController@updateLogo');
    Route::post('/config/update/advance', 'API\PreferencesController@updateAdvanceConfig');
    Route::post('/config/update/cert', 'API\PreferencesController@updateCert');

    /*Afip*/
    Route::get('/buscarAfip/{num}', 'API\ClientesController@buscarAfip');

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

    Route::get('mi_cuenta', 'API\ClientesController@miCuenta');
    Route::get('showRecibo/{id}', 'API\ClientesController@showRecibo');

    Route::get('/config/comercial', 'API\PreferencesController@getComercialConfig');

    Route::group(['middleware' => 'cors'], function () {
        Route::get('/consultar', 'API\DolarController@consultar');
    });

    Route::post('/formaPago', 'API\CuentacorrientesController@formaPago');

    Route::post('facturar', [VentasController::class, 'facturar']);

    Route::apiResource('facturas', API\FacturasController::class, ['only' => ['index', 'store', 'show']]);

    Route::post('/buscando', [BuscadorController::class, 'buscando']);
});

/*Auth*/
Route::post('/login', 'AuthController@login');
Route::post('/refresh', 'AuthController@refresh');

// Probando
Route::apiResource('negocios', API\NegociosController::class);
Route::apiResource('movimientos', API\MovimientosController::class, ['only' => ['index']]);
