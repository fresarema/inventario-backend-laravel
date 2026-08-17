<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\InventarioController;
use App\Http\Livewire\GestionMetros;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Todas las rutas dentro de este grupo requieren iniciar sesión
Route::middleware(['auth'])->group(function () {
    
    // Ruta principal del panel
    Route::get('/home', function () {
        return view('welcome'); 
    })->name('home');

    // Rutas de Modulos
    Route::get('/usuarios', function () {
        return "Aquí irá el CRUD de Usuarios";
    });

    // Rutas de CRUD de usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    // Rutas del Módulo de Inventarios
    Route::get('/inventarios', [InventarioController::class, 'index'])->name('inventarios.index');
    Route::get('/inventarios/crear', [InventarioController::class, 'create'])->name('inventarios.create');
    Route::post('/inventarios', [InventarioController::class, 'store'])->name('inventarios.store');

    // Ruta para cerrar el inventario
    Route::put('/inventarios/{id}/cerrar', [InventarioController::class, 'cerrar'])->name('inventarios.cerrar');
    Route::get('/reportes', function () {
        return view('reportes'); 
    })->name('reportes.index');

    // Ruta para ver los detalles del inventario
    Route::get('/inventarios/{id}', [InventarioController::class, 'show'])->name('inventarios.show');

    // Ruta del modulo de pasillos
    Route::get('/metros', function () {
        return view('metros.index'); 
    })->name('metros.index');

});


