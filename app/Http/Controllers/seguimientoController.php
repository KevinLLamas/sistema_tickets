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
use App\Models\Solicitud_usuario;
use App\Models\Atencion_externos;
use App\Models\Atencion_adjunto;
use App\Models\Usuario;
use App\Models\Departamentos;

class seguimientoController extends Controller 
{
	public function seguimiento($id){
		//$categoria = Categoria::all();
		//$categoria = Solicitud::with(['subcategoria','atencion'])->find($id);
		$solicitud = Solicitud::with(['subcategoria','atencion','usuario', 'dato_adicional', 'departamento', 'solicitud_usuario'])->where('id_solicitud', $id)->first();
		$atencion = $solicitud->atencion;
		foreach($atencion as $at)
		{
			$adjuntos = Atencion_adjunto::where('id_atencion', $at->id)->get();
			$at->adjuntos = $adjuntos;
		}

		$departamentos = $solicitud->departamento;
		foreach($departamentos as $dtp)
		{
			//$adjuntos = Atencion_adjunto::where('id_atencion', $at->id)->get();
			$usuarios = Usuario::where('id_departamento', $dtp->id)->get();
			$dtp->integrantes = $usuarios;
		}
		/*$departamentos = $solicitud->departamento;
		
		foreach($departamentos as $dpt)
		{
			return $dtp;
			$usuario = Usuario::where('id_departamento', $dtp->id)->get();
			$dtp->integrantes = $usuario;
		}*/
		return $solicitud;
	}
	
	public function inserta_atencion(Request $request){
		$atencion = new Solicitud_atencion;
		$Sol_atencion = $request->input('data');	
			
		$atencion->detalle = $Sol_atencion['detalle'];
		$atencion->id_solicitud = $Sol_atencion['id_solicitud'];
		$atencion->id_usuario = $Sol_atencion['id_usuario'];
		if($Sol_atencion['tipo_at'] != "")
			$atencion->tipo_at = $Sol_atencion['tipo_at'];
		else
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
		return $solicitud->estatus;
	}

	public function seguimiento_externo($id){
		return \View::make('seguimiento_externo',compact('id'));
	}

	public function verifica_codigo(Request $request)
	{
		//return $request->all();
		$id = $request->input('id');
		$codigo = $request->input('codigo');
		//Falta borrar codigo
		//return Atencion_externos::where('solicitud', $id)->where('codigo', $codigo)->toSql();
		if(Atencion_externos::where('solicitud', $id)->where('codigo', $codigo)->exists())
		{
			return response()->json([
                'status' => true, 
                'id_solicitud' =>Crypt::decryptString($id),
            ]);
		}
		else
		{
			return response()->json([
                'status' => false,
            ]);
		}
	}

	public function get_file($path, $nombre_doc)
	{
		return Storage::disk('public')->response("solicitudes/".$path."/".$nombre_doc."");
	}

	public function getUserData()
	{
		return $user = Usuario::where('id_sgu', Session::get('id_sgu'))->first(); 
		//return Session::all();
	}

	public function UpdateSolicitud_usuario(Request $request)
	{		
		$integrantes_seleccionados = $request->input('integrantes');
		$id_solicitud = $request->input('id_solicitud');		
		
		//Desactivar integrantes no seleccionados que existen	
		$integrantes = Solicitud_usuario::where('id_solicitud',$id_solicitud)->where('estado', 'Atendiendo')->get();
		if(count($integrantes_seleccionados) != 0)	
		{			
			foreach($integrantes as $integ)
			{
				foreach($integrantes_seleccionados as $integ_sel)
				{
					if($integ->id_usuario != $integ_sel)
					{
						$integ->estado = 'Suspendido';
						$integ->save();
					}
				}
			}		
		}
		else if($integrantes != null)
		{
			foreach($integrantes as $integ)
			{
				$integ->estado = 'Suspendido';
				$integ->save();	
			}
		}

		//Reactivar usuario en una solicitud o agregarlo si no existe
		$integrantes = Solicitud_usuario::where('id_solicitud',$id_solicitud)->whereIn('id_usuario', $integrantes_seleccionados)->get();
		$ban = false;
		foreach($integrantes_seleccionados as $integ_sel)
		{
			foreach($integrantes as $integ)
			{
				if($integ->id_usuario == $integ_sel)
				{
					$ban = true;
					$integ->estado = 'Atendiendo';
					$integ->save();
					$clave = array_search($integ_sel, $integrantes_seleccionados);
					unset($integrantes_seleccionados[$clave]);
					break;
				}
			}	
		}

		foreach($integrantes_seleccionados as $integ_sel)
		{
			$sol_usuario = new Solicitud_usuario;
			$sol_usuario->id_solicitud = $id_solicitud;
			$sol_usuario->id_usuario = $integ_sel;
			$sol_usuario->save();
		}
		
		
		return true;
	}

	public function getDepartamentos()
	{
		return Departamentos::All();
	}
}