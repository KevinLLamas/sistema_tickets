<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudUsuarioController;
use App\Http\Controllers\UsuarioController;
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

//Rutas dashboard
Route::get('dashboard', function () {
    return view('dashboard');
});

Route::get('lista_solicitudes', function () {
    return view('lista_solicitudes');
});

Route::get('mis_solicitudes', function () {
    return view('mis_solicitudes');
});

Route::get('solicitudes_departamento', function () {
    return view('solicitudes_departamento');
});


/////////////////////////


//Solicitud Controller
Route::post('get_Solicitudes', [SolicitudController::class, 'get_solicitudes']);
Route::get('get_Num_Solicitudes_ByStatus_General', [SolicitudController::class, 'get_num_reportes_by_status_general']);
Route::post('get_MySolicitudes_Dep', [SolicitudController::class, 'get_MySolicitudes_Dep']);

//Desde UsuarioController por IdUsuario

Route::post('get_Num_Solicitudes_ByStatus_Dep', [UsuarioController::class, 'get_num_solicitudes_by_status_dep']);
Route::post('get_Solicitudes_byUsuario', [UsuarioController::class, 'get_solicitudes_ByUsuario']);
Route::post('get_Num_Solicitudes_ByStatus_Usuario', [UsuarioController::class, 'get_num_reportes_by_status_usuario']);
Route::post('get_MySolicitudes', [UsuarioController::class, 'get_MySolicitudes']);
