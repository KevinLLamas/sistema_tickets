<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Solicitud_notificacion;
use Illuminate\Support\Facades\Session;
use App\Models\Usuario;
class NotificacionController extends Controller
{
    function get_notificaciones(Request $request){
       $notificaciones = Solicitud_notificacion::with('atencion')->where('id_usuario',Session::get('id_sgu'))->orderBy('id','DESC')->get();
       $cont = 0;
       foreach ($notificaciones as $notificacion) {
           $usuario = Usuario::find($notificacion->atencion->id_usuario);
            if(is_null($usuario))
                $notificacion->creador = (object) ['correo'=>'Usuario'];
            else
                $notificacion->creador = $usuario;
           if($notificacion->status == 'No leida')
                $cont++;
            
        }
        return response()->json([
            'ok'=> true,
            'notificaciones' => $notificaciones, 
            'cont' =>$cont,
        ]);
    } 
    function set_notificacion_leida(Request $request)
    {
        $notificacion = Solicitud_notificacion::find($request->input('id'));
        $notificacion->status = 'Leida';
        $notificacion->save();
    }
    function get_listado_notificaciones(Request $request)
    {
        $busquedaid = $request->input('busquedaid');
        $notificaciones = Solicitud_notificacion::with('atencion')
        ->where('id_usuario',Session::get('id_sgu'))
        ->where('id_solicitud','like',"%$busquedaid%")
        ->orderBy('id','DESC')
        ->get();
        $cont = 0;
        foreach ($notificaciones as $notificacion) {
           $usuario = Usuario::find($notificacion->atencion->id_usuario);
            if(is_null($usuario))
                $notificacion->creador = (object) ['correo'=>'Usuario'];
            else
                $notificacion->creador = $usuario;
        }
        return response()->json([
            'ok'=> true,
            'notificaciones' => $notificaciones, 
            'cont' =>$cont,
        ]);
    }
}
