<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [RelatorioController::class, 'personalizado'])->middleware(['auth']);
Route::get('/Filtrar-Empresa', [RelatorioController::class, 'filtrarEmpresa'])->middleware(['auth']);
Route::get('/Filtrar-Estado', [RelatorioController::class, 'filtrarEstado'])->middleware(['auth']);
Route::get('/Filtrar-Regiao', [RelatorioController::class, 'filtrarRegiao'])->middleware(['auth']);
Route::get('/Filtrar', [RelatorioController::class, 'filtrar_de_vdd'])->middleware(['auth']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
