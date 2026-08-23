<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventarioApiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Puerta de entrada pública
Route::post('/login', [InventarioApiController::class, 'login']);

// Rutas estrictamente protegidas (Requieren Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/inventarios/abiertos/{codLocal}', [InventarioApiController::class, 'getInventariosAbiertos']);
    Route::get('/productos', [InventarioApiController::class, 'getProductos']);
    Route::post('/validar-metro', [InventarioApiController::class, 'validarMetro']);
    Route::post('/sincronizar', [InventarioApiController::class, 'sincronizar']);
});