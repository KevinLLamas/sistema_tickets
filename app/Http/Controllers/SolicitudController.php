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
use App\Models\Solicitud_atencion;
use App\Models\Atencion_adjunto;
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Crypt;
use App\Models\Atencion_externos;
use App\Models\Subcategoria_departamento;
use App\Models\Solicitud_departamento;
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

        $departamentos = Subcategoria_departamento::where('id_subcategoria', $solicitud->id_subcategoria)->get();
        foreach ($departamentos as $departamento)
        {
            $solicitud_departamento = new Solicitud_departamento;
            $solicitud_departamento->id_solicitud = $id_solicitud;
            $solicitud_departamento->id_departamento = $departamento->id_departamento;
            $solicitud_departamento->aceptada = 'true';
            $solicitud_departamento->razon = '';
            $solicitud_departamento->save();
        }

        $solicitud_atencion = new Solicitud_atencion;
        $solicitud_atencion->id_solicitud = $id_solicitud;
        $solicitud_atencion->id_usuario = 1;
        $solicitud_atencion->detalle = 'Solicitud creada';
        $solicitud_atencion->tipo_respuesta = 'Todos';
        $solicitud_atencion->momento =now();
        $solicitud_atencion->tipo_at = 'Creacion';
        $solicitud_atencion->save();

        $atencion_externos = new Atencion_externos;
        $atencion_externos->solicitud =  Crypt::encryptString($id_solicitud);
        $atencion_externos->codigo =  $this->generarCodigo();
        $atencion_externos->save();
        if($this->send_mail_nueva($atencion_externos,$solicitud->correo_atencion) == 'Enviado');
            return response()->json([
                'status' => true, 
                'id_solicitud' =>$id_solicitud,
                'id_atencion' =>$solicitud_atencion->id,
            ]);
    }
    public function save_files(Request $request)
    {
        //return $request->all();
        $id_solicitud = $request->input('id_solicitud');
        $id_atencion = $request->input('id_atencion');
        $files = $request->file('files');
        if($id_solicitud > 0){
            
            $carpeta_nombre = "solicitud-$id_solicitud";
            foreach ($files as $key => $file) {
                $file_ext = $file->getClientOriginalExtension();
                if($file_ext == 'pdf' || $file_ext == 'png' || $file_ext == 'jpg' || $file_ext == 'jpeg' || $file_ext == 'xls' || $file_ext == 'PDF' || $file_ext == 'PNG' || $file_ext == 'JPG' || $file_ext == 'JPEG' || $file_ext == 'XLS'){
                    $fileName = $file->getClientOriginalName();
                    $file->storeAs($carpeta_nombre, $fileName, 'solicitudes');
                    $solicitud_adjunto = new Atencion_adjunto;
                    $solicitud_adjunto->id_atencion = $id_atencion;
                    $solicitud_adjunto->momento = now();
                    $solicitud_adjunto->mime = $file_ext;
                    $solicitud_adjunto->path_documento = "$carpeta_nombre/$fileName";
                    $solicitud_adjunto->nombre_documento = "$fileName";
                    $solicitud_adjunto->save();
                }
                else{
                    return response()->json([
                        'status'=>false,
                        'message'=>"La extensión del archivo: ".$file->getClientOriginalName()." es incorrecta.",
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
    function generarCodigo() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
        $randomString = '';
        for ($i = 0; $i < 6; $i++) { 
            $index = rand(0, strlen($characters) - 1); 
            $randomString .= $characters[$index]; 
        } 
        return $randomString; 
    } 
    public function send_mail_nueva($atencion_externos,$email){

        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP();
            $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'AKIATCA5M63WVFEMVFE3';
            $mail->Password = 'BG9yGrkHgndFSF0aJcQv1L8fFj9k+jnjHigMmpkkUSMA';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom("noreplay@jaliscoedu.mx", 'CASE');
            $mail->CharSet = 'UTF-8';
            $mail->addAddress(trim($email));

            $mail->Subject = "Confirmación de solicitud creada";
            $mail->isHTML(true);
            $headers = "Content-Type: text/html; charset=UTF-8";
            $mailContent = "
                    <p>Confirmación de solicitud creada</p>
                    <p>Usted ha creado su solicitud con éxito en el sistema CASE.</p>
                    <p>Para dar seguimiento a su solicitud de click <a href='127.0.0.1:8000/seguimiento_externo/$atencion_externos->solicitud'>aquí</a></p>
                    <p>Su código de verificación es: $atencion_externos->codigo.</p>
            ";
            $mail->Body = $mailContent;

            if(!$mail->send()){
               return $mail->ErrorInfo;
            }else{
                return 'Enviado';
            }
        }catch(phpmailerException $e){
            return $e;
        }
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

