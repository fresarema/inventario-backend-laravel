<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventarioApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoint para que la app móvil valide al operario
Route::post('/login', [InventarioApiController::class, 'login']);

// Rutas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/productos', [App\Http\Controllers\Api\InventarioController::class, 'productos']);
    Route::post('/inventario/sincronizar', [App\Http\Controllers\Api\InventarioApiController::class, 'sincronizar']);
});


// 1. Endpoint para que Flutter descargue los inventarios disponibles en un local
Route::get('/inventarios/abiertos/{codLocal}', [InventarioApiController::class, 'getInventariosAbiertos']);

// 2. Endpoint para que Flutter descargue el catálogo maestro de productos (Sin el stock)
Route::get('/productos', [InventarioApiController::class, 'getProductos']);

// 3. Endpoint para que Flutter envíe los productos escaneados
Route::post('/sincronizar', [InventarioApiController::class, 'sincronizar']);

Route::post('/validar-metro', [InventarioApiController::class, 'validarMetro']);