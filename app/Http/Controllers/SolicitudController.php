<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use Illuminate\Support\Facades\DB;
use App\Models\Subcategoria;
use App\Models\Campo_perfil_subcategoria;
use App\Models\Campo_personalizado;
use App\Models\Solicitud_dato_adicional;
use App\Models\Solicitud_adjunto;
class SolicitudController extends Controller
{
    public function getCampos(Request $request)
    {
        $id_subcategoria = $request->input('id_subcategoria');
        $id_perfil = $request->input('id_perfil');
        $id_campos = Campo_perfil_subcategoria::where('id_perfil', $id_perfil)->where('id_subcategoria', $id_subcategoria)->get();
        $campos = array();
        foreach ($id_campos as $id_campo) {
            $campo = Campo_personalizado::with('opciones')->find($id_campo->id_campo_personalizado);
            if(!is_null($campo))
                array_push ($campos, $campo);
        }
        return $campos;
    }
    public function guardar(Request $request)
    {
        //return $request->all();
        $solicitud = new Solicitud;
        $solicitud->id_usuario = 1;
        $solicitud->estatus = "Sin atender";
        $solicitud->medio_reporte = "Internet"; 
        $solicitud->id_perfil = $request->input('solicitud.perfil');     
        $solicitud->id_subcategoria = $request->input('solicitud.subcategoria');
        $solicitud->descripcion = $request->input('solicitud.descripcion');
        $solicitud->correo_atencion = $request->input('solicitud.correo_contacto');
        $solicitud->necesita_respuesta = $request->input('solicitud.necesita');
        $solicitud->fecha_creacion = now();
        $solicitud -> save();
        $id_solicitud = $solicitud->id_solicitud;

        $datos = $request->input('datos');
        foreach ($datos as $key)
        {
            $solicitud_dato = new Solicitud_dato_adicional;
            $solicitud_dato->id_solicitud = $id_solicitud;
            $solicitud_dato->valor = $key['respuesta'];
            $solicitud_dato->tipo_dato = $key['model'];
            $solicitud_dato -> save();
        }

        
        return response()->json([
            'status' => true,
            'id_solicitud' =>$id_solicitud
        ]);
    }
    public function save_files(Request $request)
    {
        //return $request->all();
        $id_solicitud = $request->input('id_solicitud');
        $files = $request->file('files');
        if($id_solicitud > 0){
            $carpeta_nombre = "solicitud-$id_solicitud";
            foreach ($files as $key => $file) {
                $file_ext = $file->getClientOriginalExtension();
                if($file_ext == 'pdf' || $file_ext == 'png' || $file_ext == 'jpg' || $file_ext == 'jpeg' || $file_ext == 'xls'){
                    $fileName = $file->getClientOriginalName();
                    $file->storeAs($carpeta_nombre, $fileName, 'solicitudes');
                    $solicitud_adjunto = new Solicitud_adjunto;
                    $solicitud_adjunto->id_solicitud = $id_solicitud;
                    $solicitud_adjunto->momento = now();
                    $solicitud_adjunto->mime = $file_ext;
                    $solicitud_adjunto->path_documento = "$carpeta_nombre/$fileName";
                    $solicitud_adjunto->save();
                }
                else{
                    return response()->json([
                        'status'=>false,
                        'message'=>"La extención del archivo: ".$file->getClientOriginalName()." es incorrecta.",
                    ], 200);
                }
            }
            return response()->json([
                'status' => true,
                'data' =>''
            ]); 
        }
        return response()->json([
            'status' => false,
            'data' =>''
        ]); 
    }
    public function buscar_usuario(Request $request)
    {
        $id_perfil = $request->input('perfil');
        $curp = $request->input('curp');
        if($id_perfil == 1)
            return $this->buscarEstudiante($curp);
        else if($id_perfil == 2)
            return $this->buscarEstructuraEducativa($curp);
        else if($id_perfil == 3)
            return $this->buscarServidorPublico($curp);
        else
            return response()->json([
                'status' => false,
                'data' =>''
            ]); 
    }
    public function buscarEstudiante($curp){
        //Aqui vamos llamar el web service de sgu
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
            if($res['ok']== true){
                return response()->json([
                    'status' => true,
                    'data' => $res,
                ]);  
            } 
            else
            {
                return response()->json([
                    'ok'=>false,
                    'data'=>''
                ], 500);
            }
    
        }catch(Exception $e){
            return response()->json([
                'ok'=>false,
                'data'=>''
            ], 500);
        }
    }
    public function buscarEstructuraEducativa($curp){
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
            if($res['ok']== true){
                return response()->json([
                    'ok' => true,
                    'data' => $res,
                ]);  
            } 
            else
            {
                return response()->json([
                    'ok'=>false,
                    'data'=>''
                ], 500);
            }
    
        }catch(Exception $e){
            return response()->json([
                'status'=>false,
                'data'=>''
            ], 500);
        }
    }
    public function buscarServidorPublico($curp){
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
            if($res['ok']== true){
                return response()->json([
                    'ok' => true,
                    'data' => $res,
                ]);  
            } 
            else
            {
                return response()->json([
                    'ok'=>false,
                    'data'=>''
                ], 500);
            }
    
        }catch(Exception $e){
            return response()->json([
                'ok'=>false,
                'data'=>''
            ], 500);
        }
    }
    public function get_solicitudes(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $estado=$request->input('estado');
        $solicitud=Solicitud::with('usuario_many')
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
        $solicitudes_dep=Solicitud::with('usuario_many','usuario.departamento')
        
        ->whereHas('usuario.departamento',function($q)use($idDep){
            $q->where('id',$idDep);
        })
        ->paginate($num);
        return $solicitudes_dep;
    }
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

