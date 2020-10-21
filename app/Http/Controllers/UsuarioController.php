<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
class UsuarioController extends Controller
{
    
    public function get_MySolicitudes(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $estado=$request->input('estado');
        $idUsuario=$request->input('idUsuario');
        $solicitud_usuario=Usuario::find(1)->solicitudes()
        ->where('solicitud.descripcion','like',"%$busqueda%")
        ->where('solicitud.medio_reporte','like',"%$medio%")
        ->where('solicitud.estatus','like',"%$estado%")

        ->paginate($num);
       
        return $solicitud_usuario;
        
    }
    public function get_num_reportes_by_status_usuario(Request $request){
        
        $idUsuario=$request->input('idUsuario');
        $num_status=Usuario::find($idUsuario)
        ->solicitudes()
        ->select('solicitud.*',DB::raw('count(*) as total'))
        ->groupBy('solicitud.estatus')->orderBy('total','DESC')->get();
        return $num_status;
    }
    
    /**public function get_all_solicitudes(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $solicitudes_usuarios=Usuario::with(['solicitud','usuario'])->whereHas('solicitudes',function($query)use($busqueda,$medio){
            return $query->where('descripcion','like',"%$busqueda%")->where('medio_reporte','like',"%$medio%");
        })->toSql();
        return $solicitudes_usuarios;
    }**/

    
}