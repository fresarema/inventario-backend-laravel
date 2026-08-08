<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Ruta pública (cualquiera puede intentar logearse)
Route::post('/login', [App\Http\Controllers\Api\InventarioController::class, 'login']);

// Rutas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/productos', [App\Http\Controllers\Api\InventarioController::class, 'productos']);
    Route::post('/inventario/sincronizar', [App\Http\Controllers\Api\InventarioController::class, 'sincronizar']);
});