<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Validator;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\UserController;
use App\Models\Categoria;
use App\Models\Solicitud;
use App\Models\Solicitud_atencion;


class seguimientoController extends Controller {
	public function seguimiento($id){
		//$categoria = Categoria::all();
		//$categoria = Solicitud::with(['subcategoria','atencion'])->find($id);
		return Solicitud::with(['subcategoria','atencion','usuario', 'dato_adicional'])->where('id_solicitud', $id)->first();
	}

	public function inserta_atencion(Request $request){
		$atencion = new Solicitud_atencion;
		$Sol_atencion = $request->input('data');	
			
		$atencion->detalle = $Sol_atencion['detalle'];
		$atencion->id_solicitud = $Sol_atencion['id_solicitud'];
		$atencion->id_usuario = $Sol_atencion['id_usuario'];
		$atencion->tipo_at = 'Atencion';
		$atencion->tipo_respuesta = $Sol_atencion['tipo_respuesta'];
		$atencion->save();

		return $atencion->id;
	}

	public function cambiar_estatus(Request $request)
	{
		$id = $request->input('id');
		$solicitud = Solicitud::find($id);
		$solicitud->estatus =  $request->input('estatus');
		$solicitud->save();
	}
}