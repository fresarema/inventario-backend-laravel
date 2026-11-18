<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\InventarioController;
use App\Http\Livewire\InventarioComponent; // <-- Inyectamos el componente
use App\Http\Livewire\GestionMetros;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    
    // Ruta principal del panel
    Route::get('/home', function () {
        return view('welcome'); 
    })->name('home');

    // Rutas de CRUD de usuarios 
    Route::middleware(['can:is_superadmin'])->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    });

    Route::get('/monitoreo', function () {
        return view('monitoreo.index'); 
    })->name('monitoreo.index');
    

    Route::get('/inventarios', InventarioComponent::class)->name('inventarios.index');
    // 2. Ruta para ver los detalles del inventario 
    Route::get('/inventarios/{id}', [InventarioController::class, 'show'])->name('inventarios.show');

    Route::get('/reportes', function () {
        return view('reportes'); 
    })->name('reportes.index');

    // Ruta del modulo de pasillos
    Route::get('/metros', function () {
        return view('metros.index'); 
    })->name('metros.index');
});

