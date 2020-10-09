<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\seguimientoController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ejemplo', function () {
    return view('ejemplo');
});
Route::get('getSolicitud/{id}', [seguimientoController::class, 'seguimiento']);
Route::get('seguimiento/{id}', function () {return view('seguimiento');}); 
