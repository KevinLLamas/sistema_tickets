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
use App\Http\Controllers\NotificacionController;

Route::group(['middleware' => 'validar', 'web'], function()
{
Route::get('logout', [LoginController::class, 'logout']);
//SOLICITUDES
Route::get('/alta_ticket', function(){return view('alta_solicitud');});
Route::post('guardar_solicitud', [SolicitudController::class, 'guardar']);
Route::post('save_files', [SolicitudController::class, 'save_files']);
Route::get('getCampos', [SolicitudController::class, 'getCampos']);
Route::post('buscar_usuario', [SolicitudController::class, 'buscar_usuario']);
//obtener solicitudes
Route::post('get_solicitudes_admin', [SolicitudController::class, 'get_solicitudes_admin']);
Route::post('get_solicitudes_departamento', [SolicitudController::class, 'get_solicitudes_departamento']);
Route::post('get_mis_solicitudes', [SolicitudController::class, 'get_mis_solicitudes']);
Route::post('get_solicitudes_asignadas', [SolicitudController::class, 'get_solicitudes_asignadas']);
Route::post('get_ticket', [SolicitudController::class, 'get_ticket']);

//datos para graficas
Route::get('get_num_solicitudes_bystatus_admin', [SolicitudController::class, 'get_num_solicitudes_bystatus_admin']);
Route::get('get_num_solicitudes_bystatus_mis_solicitudes', [SolicitudController::class, 'get_num_solicitudes_bystatus_mis_solicitudes']);
Route::get('get_num_solicitudes_bystatus_departamento', [SolicitudController::class, 'get_num_solicitudes_bystatus_departamento']);
Route::get('get_num_solicitudes_bystatus_asignadas', [SolicitudController::class, 'get_num_solicitudes_bystatus_asignadas']);

//reportes
Route::post('get_num_solicitudes_through_time', [SolicitudController::class, 'get_num_solicitudes_through_time']);
Route::post('get_num_solicitudes_through_time_cerradas', [SolicitudController::class, 'get_num_solicitudes_through_time_cerradas']);
Route::get('get_usuarios_by_departamento', [SolicitudController::class, 'get_usuarios_by_departamento']);
Route::post('get_num_solicitudes_by_estatus_usuario', [SolicitudController::class, 'get_num_solicitudes_by_estatus_usuario']);
Route::post('get_solicitudes_departamento_rep', [SolicitudController::class, 'get_solicitudes_departamento_rep']);
Route::post('get_porcentaje_cerradas', [SolicitudController::class, 'get_porcentaje_cerradas']);



//SOLICITAR SERVICIO
Route::get('/alta_solicitud_servicio', function(){return view('alta_solicitud_servicio');});

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
Route::get('solicitudes_asignadas', function () {return view('solicitudes_asignadas');});
Route::get('solicitudes_departamento', function () {return view('solicitudes_departamento');});
Route::get('charts', function () {return view('charts');});
Route::get('reportes', function () {return view('reportes');});

//Seguimiento
Route::get('seguimiento/{id}', function () {return view('seguimiento');}); 

//NOTIFICACIONES
Route::post('get_notificaciones', [NotificacionController::class, 'get_notificaciones']);
Route::post('set_notificacion_leida', [NotificacionController::class, 'set_notificacion_leida']);
Route::post('get_listado_notificaciones', [NotificacionController::class, 'get_listado_notificaciones']);
Route::get('notificaciones', function () {return view('notificaciones');}); 


});
Route::get('prueba/{id}', [seguimientoController::class, 'desencriptar']); 
Route::get('getSolicitud/{id}', [seguimientoController::class, 'seguimiento']);
Route::post('inserta_atencion',  [seguimientoController::class, 'inserta_atencion']);
Route::post('cambiar_estatus',  [seguimientoController::class, 'cambiar_estatus']);
Route::get('seguimiento_externo/{id}', [seguimientoController::class, 'seguimiento_externo']); 
Route::post('verifica_codigo', [seguimientoController::class, 'verifica_codigo']); 
Route::get('get_file/{path}/{nombre_doc}', [seguimientoController::class, 'get_file']); 
Route::get('getUserData', [seguimientoController::class, 'getUserData']);
Route::post('UpdateSolicitud_usuario',  [seguimientoController::class, 'UpdateSolicitud_usuario']);
Route::get('getDepartamentos', [seguimientoController::class, 'getDepartamentos']);
Route::post('inserta_atencion_externo',  [seguimientoController::class, 'inserta_atencion_externo']);

Route::get('/ejemplo', function () {
    return view('ejemplo');
});


Route::get('getUsers', [seguimientoController::class, 'getUsers']); 