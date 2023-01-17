<?php

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

Route::get('/', [RelatorioController::class, 'index']);
Route::get('/Filtrar-Empresa', [RelatorioController::class, 'filtrarEmpresa']);
Route::get('/Filtrar-Estado', [RelatorioController::class, 'filtrarEstado']);
Route::get('/Filtrar-Regiao', [RelatorioController::class, 'filtrarRegiao']);
Route::get('/Filtrar', [RelatorioController::class, 'filtrar']);
