<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SubcategoriaController;

Route::get('/', function(){return view('welcome');});
Route::get('ejemplo', function(){return view('ejemplo');});

//SOLICITUDES
Route::get('alta_solicitud', function(){return view('alta_solicitud');});
Route::post('guardar_solicitud', [SolicitudController::class, 'guardar']);
Route::post('save_files', [SolicitudController::class, 'save_files']);
Route::get('getCampos', [SolicitudController::class, 'getCampos']);
Route::post('buscar_usuario', [SolicitudController::class, 'buscar_usuario']);

//CATEGORIAS
Route::get('categorias', [CategoriaController::class, 'categorias']);

//SUBCATEGORIAS
Route::get('subcategorias', [SubcategoriaController::class, 'subcategorias']);

//PERFILES
Route::get('perfiles', [PerfilController::class, 'perfiles']);