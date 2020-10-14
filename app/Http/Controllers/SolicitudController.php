<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use Illuminate\Support\Facades\DB;
class SolicitudController extends Controller
{
    /*public function get_solicitudes(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $solicitud=Solicitud::where('descripcion','like',"%$busqueda%")->where('medio_reporte','like',"%$medio%")->paginate($num);
        return $solicitud;
    }*/
    /**public function get_solicitudes(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $solicitud=Solicitud::join('solicitud_usuario','solicitud_usuario.id_solicitud','=','solicitud.id_solicitud')->join('usuario','usuario.id','=','solicitud_usuario.id_usuario')->where('solicitud.descripcion','like',"%$busqueda%")->where('solicitud.medio_reporte','like',"%$medio%")->paginate($num);
        return $solicitud;
        
    }**/
    public function get_solicitudes(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $estado=$request->input('estado');
        $solicitud=Solicitud::with('usuario')
            ->where('estatus','like',"%$estado%")
            ->where('descripcion','like',"%$busqueda%")
            ->where('medio_reporte','like',"%$medio%")->paginate($num);
        return $solicitud;
        
    }
    public function get_MySolicitudes_Dep(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $estado=$request->input('estado');
        $idUsuario=$request->input('idUsuario');
        $idDep=$request->input('idDep');
        $solicitudes_dep=Solicitud::with('usuario','usuario.departamento')
        
        ->whereHas('usuario.departamento',function($q)use($idDep){
            $q->where('id',$idDep);
        })
        ->paginate($num);
        return $solicitudes_dep;
    }

    /*public function get_num_reportes_by_status(){
        
        $num_status=Solicitud::select('solicitud.estatus',DB::raw('count(*) as total'))->join('solicitud_usuario','solicitud_usuario.id_solicitud','=','solicitud.id_solicitud')->join('usuario','usuario.id','=','solicitud_usuario.id_usuario')->groupBy('solicitud.estatus')->orderBy('total','DESC')->get();
        return $num_status;
    }*/
    public function get_num_reportes_by_status_general(){
        
        $num_status=Solicitud::select('solicitud.estatus',DB::raw('count(*) as total'))->groupBy('solicitud.estatus')->orderBy('total','DESC')->get();
        return $num_status;
    }
    
    public function get_num_reportes_by_status_dep(){
        
        $num_status=Solicitud::select('solicitud.estatus',DB::raw('count(*) as total'))->groupBy('solicitud.estatus')->orderBy('total','DESC')->get();
        return $num_status;
    }
    public function get_num_reportes_by_medio(){

    }
    public function insert(Request $request){

        //Insert a tabla Solicitud
        $solicitud=new Solicitud;
        $solicitud->id_usuario=3;
        $solicitud->descripcion=$request->input('descripcion');
        $solicitud->estatus="Sin atender";
        $solicitud->medio_reporte="Internet";
        $solicitud->save();
        $id_solicitud = $solicitud->id_solicitud;
        
        //Insert a tabla datos adicionales
        $datos=$request->input('datos');
        foreach($datos as $key){
            $solicitud_dato=new Solicitud_Dato;
            $solicitud_dato->id_solicitud = $id_solicitud;
            $solicitud_dato->valor = $key['valor'];
            $solicitud_dato->tipo_dato = $key['tipo_dato'];
            $solicitud_dato->save();
        }

        //Insert a tabla solicitud_subcategoria
        $solicitud_subcategoria=new Solicitud_Subcategoria;
        $solicitud_subcategoria->id_solicitud=$id_solicitud;
        $solicitud_subcategoria->$request->input('subcategoria');
        $solicitud_subcategoria->save();

        return $id_solicitud;
    }

    public function buscar_alumno(Request $request)
    {
        //Aqui vamos llamar el web service de sgu
        $curp = $request->input('curp'); 
        try{
            $data = array(
                'curp'=>$curp,
                'llaveApp' => "43C878ED437AC2E688BCE67B01F6DF440202AC37ADD790E98A353BDE6AE97940"
            );
            $url = curl_init("http://10.9.4.152:3000/buscarPorCurp");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_POSTFIELDS, http_build_query($data));
            $response = curl_exec($url);
            curl_close($url);
            $res = \json_decode($response, true);
            //dd($res);
            if($res['ok']== true){
                
                
                return response()->json([
                    'status' => true,
                    'data' => $res,
                ]);  
               
            } if(isset($res['err'])){
                //Buscamos en Control Escolar los datos
                $data = array(
                   'curp'=>$curp
                );
                $url = curl_init("http://10.9.4.222:3001/getAlumno");
                curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($url, CURLOPT_POSTFIELDS, http_build_query($data));
                $response = curl_exec($url);
                curl_close($url);
    
                $res = \json_decode($response, true);
                if($res['ok']==true){
                    return response()->json([
                        'status' => true,
                        'data' =>$res
                    ]);  
                }
            } else {
                return response()->json([
                    'status' => false,
                    'data' =>''
                ]);  
            }  
    
        }catch(Exception $e){
            return response()->json([
                'ok'=>false,
                'data'=>''
            ], 500);
        }
        
    }
}

