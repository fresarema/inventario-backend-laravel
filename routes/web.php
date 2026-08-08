<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Estudiantes;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// RUTA DEL MANTENEDOR DE ESTUDIANTES (LIVEWIRE)
Route::get('/estudiantes',Estudiantes::class)->middleware('auth')->name('estudiantes.index');


