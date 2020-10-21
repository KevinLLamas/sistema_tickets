<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudUsuarioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\seguimientoController;
use App\Http\Controllers\LoginController;


Route::group(['middleware' => 'validar', 'web'], function()
{
Route::get('logout', [LoginController::class, 'logout']);
//SOLICITUDES
Route::get('/alta_solicitud', function(){return view('alta_solicitud');});
Route::post('guardar_solicitud', [SolicitudController::class, 'guardar']);
Route::post('save_files', [SolicitudController::class, 'save_files']);
Route::get('getCampos', [SolicitudController::class, 'getCampos']);
Route::post('buscar_usuario', [SolicitudController::class, 'buscar_usuario']);
Route::post('get_Solicitudes', [SolicitudController::class, 'get_solicitudes']);
Route::get('get_Num_Solicitudes_ByStatus_General', [SolicitudController::class, 'get_num_reportes_by_status_general']);
Route::post('get_MySolicitudes_Dep', [SolicitudController::class, 'get_MySolicitudes_Dep']);

//CATEGORIAS
Route::get('categorias', [CategoriaController::class, 'categorias']);

//SUBCATEGORIAS
Route::get('subcategorias', [SubcategoriaController::class, 'subcategorias']);

//PERFILES
Route::get('perfiles', [PerfilController::class, 'perfiles']);

//DASHBOARD
Route::get('dashboard', function () {return view('dashboard');});
Route::get('/', function () {return view('dashboard');});
Route::get('lista_solicitudes', function () {return view('lista_solicitudes');});
Route::get('mis_solicitudes', function () {return view('mis_solicitudes');});
Route::get('solicitudes_departamento', function () {return view('solicitudes_departamento');});

//USUARIO
Route::post('get_Num_Solicitudes_ByStatus_Dep', [UsuarioController::class, 'get_num_solicitudes_by_status_dep']);
Route::post('get_Solicitudes_byUsuario', [UsuarioController::class, 'get_solicitudes_ByUsuario']);
Route::post('get_Num_Solicitudes_ByStatus_Usuario', [UsuarioController::class, 'get_num_reportes_by_status_usuario']);
Route::post('get_MySolicitudes', [UsuarioController::class, 'get_MySolicitudes']);
});

//Seguimiento
Route::get('/ejemplo', function () {
    return view('ejemplo');
});
Route::get('getSolicitud/{id}', [seguimientoController::class, 'seguimiento']);
Route::get('seguimiento/{id}', function () {return view('seguimiento');}); 
Route::post('inserta_atencion',  [seguimientoController::class, 'inserta_atencion']);
Route::post('cambiar_estatus',  [seguimientoController::class, 'cambiar_estatus']);
Route::get('seguimiento_externo/{id}', [seguimientoController::class, 'seguimiento_externo']); 
Route::post('verifica_codigo', [seguimientoController::class, 'verifica_codigo']); 
Route::get('get_file/{path}/{nombre_doc}', [seguimientoController::class, 'get_file']); 
