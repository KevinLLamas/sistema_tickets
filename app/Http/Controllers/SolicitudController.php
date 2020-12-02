<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Usuario;
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
use App\Models\Departamentos;
use App\Models\Solicitud_usuario;
use App\Models\Solicitud_notificacion;
use Illuminate\Support\Facades\Session;
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
        if(filter_var($request->input('solicitud.correo_contacto'), FILTER_VALIDATE_EMAIL))
        {
            //CREAMOS SOLICITUD
            $solicitud = new Solicitud;
            $solicitud->id_usuario = Session::get('id_sgu');
            $solicitud->estatus = "Sin atender";
            $solicitud->medio_reporte = "Internet"; 
            $solicitud->id_perfil = $request->input('solicitud.perfil');     
            $solicitud->id_subcategoria = $request->input('solicitud.subcategoria');
            $solicitud->id_categoria = $request->input('solicitud.categoria');
            $solicitud->descripcion = $request->input('solicitud.descripcion');
            $solicitud->correo_atencion = $request->input('solicitud.correo_contacto');
            $solicitud->necesita_respuesta = $request->input('solicitud.necesita');
            $solicitud->fecha_creacion = now();
            $solicitud -> save();
            $id_solicitud = $solicitud->id_solicitud;

            //AGREGAMOS DATOS ADICIONALES A LA SOLICITUD
            $datos = $request->input('datos');
            foreach ($datos as $key){
                $solicitud_dato = new Solicitud_dato_adicional;
                $solicitud_dato->id_solicitud = $id_solicitud;
                $solicitud_dato->valor = $key['respuesta'];
                $solicitud_dato->tipo_dato = $key['model'];
                $solicitud_dato -> save();
            }

            //AGREGAMOS EL MENSAJE DE SOLICITUD CREADA
            $solicitud_atencion = new Solicitud_atencion;
            $solicitud_atencion->id_solicitud = $id_solicitud;
            $solicitud_atencion->id_usuario = Session::get('id_sgu');;
            $solicitud_atencion->detalle = 'Ticket creado';
            $solicitud_atencion->tipo_respuesta = 'Todos';
            $solicitud_atencion->momento =now();
            $solicitud_atencion->tipo_at = 'Creacion';
            $solicitud_atencion->save();

            //BUSCAMOS Y ASIGNAMOS A LOS DEPARTAMENTOS A LOS QUE PERTENECE LA SOLICITUD
            $departamentos = Subcategoria_departamento::where('id_subcategoria', $solicitud->id_subcategoria)->where('primario', 'true')->get();
            foreach ($departamentos as $departamento)
            {
                $solicitud_departamento = new Solicitud_departamento;
                $solicitud_departamento->id_solicitud = $id_solicitud;
                $solicitud_departamento->id_departamento = $departamento->id_departamento;
                $solicitud_departamento->aceptada = 'true';
                $solicitud_departamento->razon = '';
                $solicitud_departamento->save();
                
                //TRAEMOS TECNICOS DE EL DEPARTAMENTO ACTUAL
                $usuarios_depa = Usuario::with('ultima_asignada')
                ->where('id_departamento', $departamento->id_departamento)
                ->where('id_sgu','!=','1')
                ->where('rol','TECNICO')
                ->get();

                //BUSCAMOS A EL USUARIO EL CUAL TENGA MAS TIEMPO SIN QUE SE LE ASIGNE UNA SOLICITUD
                $usuario_asignar = $usuarios_depa[0];
                foreach ($usuarios_depa as $usuario) {
                    if(is_null($usuario->ultima_asignada)){
                        $usuario_asignar = $usuario;
                        break;
                    }
                    if(new \DateTime($usuario->ultima_asignada->momento) < new \DateTime($usuario_asignar->ultima_asignada->momento))
                        $usuario_asignar = $usuario;
                }
                //ASIGNAMOS LA SOLICITUD A EL USUARIO DE ESTE DEPARTAMENTO
                $solicitud_usuario = new Solicitud_usuario;
                $solicitud_usuario->id_solicitud = $id_solicitud;
                $solicitud_usuario->id_usuario = $usuario_asignar->id_sgu;
                $solicitud_usuario->momento = now();
                $solicitud_usuario->estado = 'Atendiendo';
                $solicitud_usuario->save();

                //CREAMOS EL MENSAJE DE ATENCION Y ASIGNACION
                $atencion = new Solicitud_atencion;
                $atencion->id_solicitud = $id_solicitud;
                //$atencion->id_usuario = $id_solicitud;
                $atencion->detalle = 'asignó a '.$this->get_usuario($usuario_asignar->id_sgu)['nombre'].' a este ticket.';
                $atencion->tipo_respuesta = 'Todos';
                $atencion->tipo_at = 'Asignacion';
                $atencion->momento = now();
                $atencion->save();

                //CREAMOS LA NOTIFICACION PARA EL ASIGNADO
                $notificacion = new Solicitud_notificacion;
                $notificacion->id_solicitud = $id_solicitud;
                $notificacion->id_atencion = $atencion->id;
                $notificacion->id_usuario = $usuario_asignar->id_sgu;
                $notificacion->status = 'No leida';
                $notificacion->save();

                //ACTUALIZAMOS EL ESTATUS DE LA SOLICITUD
                if($solicitud->estatus == 'Sin atender'){
                    $solicitud->estatus = 'Atendiendo';
                    $solicitud->save();
                }
            }

            
            //GUARDAMOS INFORMACION PARA EL USUARIO EXTERNO
            $atencion_externos = new Atencion_externos;
            $atencion_externos->solicitud =  $this->encriptar($id_solicitud);
            $atencion_externos->id_solicitud = $id_solicitud; //->Esta es la linea funciona al agregar el campo a la tabla
            $atencion_externos->codigo =  $this->generarCodigo();
            $atencion_externos->save();

            //MANDAMOS LA INFORMACION POR CORREO
            if($this->send_mail_nueva($atencion_externos,$solicitud->correo_atencion,$id_solicitud) == 'Enviado');
                return response()->json([
                    'status' => true, 
                    'id_solicitud' =>$id_solicitud,
                    'id_atencion' =>$solicitud_atencion->id,
                ]);
            return response()->json([
                'status' => false, 
                'message' => 'Fue imposible enviar el correo de confirmación.'
            ]);
        }
        else
        {
            return response()->json([
                'status' => false, 
                'message' => 'Correo invalido.'
            ]);
        }
    }
    public function get_ticket(Request $request)
    {
        $id_ticket = $request->input('id');
        $tickets = Solicitud::where('id_solicitud','like',"%$id_ticket%")->get();
        return $tickets;
    }
    private function get_usuario($id){
		$url = curl_init("http://10.9.4.152:3000/persona");
		$llaveApp = "B0342DEF578109AD4C32E158B2702E884645493F84A0AFACA05A017D3E68D3F8";
		$data = array(
			"id_persona"=>$id,
			"llaveApp" => $llaveApp
		);
		curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($url, CURLOPT_POSTFIELDS,http_build_query($data));
		$response = curl_exec($url);
		curl_close($url);
		$res = json_decode($response, true);
		return $res;
	}
    private function encriptar($texto)
    {
        $newEncrypter = new \Illuminate\Encryption\Encrypter(base64_decode('CcUAOtSqoNvtEfMKG3FmhsOQIBiiDYL7ZQxppYG82WI='), "AES-256-CBC" );
        return $encrypted = $newEncrypter->encrypt($texto);
    }
    private function desecriptar($texto)
    {
        $newEncrypter = new \Illuminate\Encryption\Encrypter(base64_decode('CcUAOtSqoNvtEfMKG3FmhsOQIBiiDYL7ZQxppYG82WI='), "AES-256-CBC" );
        return $decrypted = $newEncrypter->decrypt($texto);
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
    public function send_mail_nueva($atencion_externos,$email,$id_solicitud){

        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP();
            $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'AKIATCA5M63WVFEMVFE3';
            $mail->Password = 'BG9yGrkHgndFSF0aJcQv1L8fFj9k+jnjHigMmpkkUSMA';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom("noreplay@jaliscoedu.mx", 'SASS');
            $mail->CharSet = 'UTF-8';
            $mail->addAddress(trim($email));

            $mail->Subject = "Confirmación de ticket creado.";
            $mail->isHTML(true);
            $headers = "Content-Type: text/html; charset=UTF-8";
            $mailContent = "
                    <p>Usted ha creado el ticket #$id_solicitud con éxito en el sistema SAS.</p><br>
                    <p>ID de ticket: $id_solicitud</p>
                    <p>Para dar seguimiento a su ticket, <a href='https://plataformadigital.sej.jalisco.gob.mx/sass/seguimiento_externo/$atencion_externos->solicitud'>por favor ingrese a este enlace.</a></p>
                    <p>Su código de verificación es: $atencion_externos->codigo</p>
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
    public function get_solicitudes_admin(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $estado=$request->input('estado');
        $id=$request->input('id');
        $orden=$request->input('orden');
        try{
            $solicitud=Solicitud::with('usuario_many')
                ->where('id_solicitud','like',"%$id%")
                ->where('estatus','like',"%$estado%")
                ->where('descripcion','like',"%$busqueda%")
                ->where('medio_reporte','like',"%$medio%")
                ->orderBy('id_solicitud',$orden)
                ->paginate($num);
            return $solicitud;
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }
    }
    public function get_solicitudes_departamento(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $estado=$request->input('estado');
        $id_solicitud=$request->input('id_solicitud');
        $idDep=Session::get('id_departamento');
        $orden=$request->input('orden');
        
        
        try{
            $solicitudes_dep=Departamentos::find($idDep)->solicitudes()
                ->where('solicitud.id_solicitud','like',"%$id_solicitud%")
                ->where('solicitud.descripcion','like',"%$busqueda%")
                ->where('solicitud.medio_reporte','like',"%$medio%")
                ->where('solicitud.estatus','like',"%$estado%")
                ->with('usuario_many')
                ->orderBy('solicitud.id_solicitud',$orden)
            ->paginate($num);
             return $solicitudes_dep;
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }
    }
    public function get_solicitudes_asignadas(Request $request){
        $idUsuario= Session::get('id_sgu');
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $estado=$request->input('estado');
        $id=$request->input('id');
        $orden=$request->input('orden');
        try{
            $solicitud_usuario = Usuario::find($idUsuario);
            if(!is_null($solicitud_usuario))
            {
                $solicitudes = $solicitud_usuario->solicitudes_atendiendo()
                ->where('solicitud.id_solicitud','like',"%$id%")
                ->where('solicitud.descripcion','like',"%$busqueda%")
                ->where('solicitud.medio_reporte','like',"%$medio%")
                ->where('solicitud.estatus','like',"%$estado%")
                ->orderBy('solicitud.id_solicitud',$orden)
                ->paginate($num);
                return $solicitudes;
            }
            else
                return response()->json([
                    'status' => false,
                    'data' =>''
                ]);
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }
        

        
    }
    public function get_mis_solicitudes(Request $request){
        
        $idUsuario= Session::get('id_sgu');
        $busqueda = $request->input('busqueda');
        $busquedaid = $request->input('busquedaid');
        $page = $request->input('page');
        $num = $request->input('num');
        $medio = $request->input('medio');
        $estado = $request->input('estado');
        $orden = $request->input('orden');
        try{
            $solicitud_usuario = Solicitud::where('id_usuario',$idUsuario)
            ->where('id_solicitud','like',"%$busquedaid%")
            ->where('descripcion','like',"%$busqueda%")
            ->where('medio_reporte','like',"%$medio%")
            ->where('estatus','like',"%$estado%")
            ->orderBy('id_solicitud',$orden)
            ->paginate($num);
       
        return $solicitud_usuario;
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }
        
        
    }
    
    public function get_num_solicitudes_bystatus_admin(){
        try{
            $num_status=Solicitud::select('solicitud.estatus',DB::raw('count(*) as total'))
            ->groupBy('solicitud.estatus')
            ->orderBy('total','DESC')
            ->get();
            return $num_status;
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }
    }
    public function get_num_solicitudes_bystatus_departamento(Request $request){
        
        $idDep=Session::get('id_departamento');
        
        try{
            $num_status=Departamentos::find($idDep)
            ->solicitudes()
            ->select('solicitud.*',DB::raw('count(*) as total'))
            ->groupBy('solicitud.estatus')->orderBy('total','DESC')->get();
            return $num_status;
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }

    }
    public function get_num_solicitudes_bystatus_asignadas(Request $request){
        
        $idUsuario=Session::get('id_sgu');
        try{
            $num_status=Usuario::find($idUsuario)
            ->solicitudes()
            ->select('solicitud.estatus',DB::raw('count(*) as total'))
            ->groupBy('solicitud.estatus')->orderBy('total','DESC')->get();
            return $num_status;
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }

    }
    public function get_num_solicitudes_bystatus_mis_solicitudes(Request $request){
        $idUsuario=Session::get('id_sgu');
        
        try{
            $num_status=Solicitud::where('id_usuario',$idUsuario)
            ->select('solicitud.estatus',DB::raw('count(*) as total'))
            ->groupBy('solicitud.estatus')->orderBy('total','DESC')->get();
            return $num_status;
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }
    }
    public function get_num_solicitudes_through_time(Request $request){
        $idUsuario=Session::get('id_sgu');
        $rangoTiempo=$request->input('rangoTiempo');
        $idDepartamento=Session::get('id_departamento');
        
        try{
            switch($rangoTiempo){
                case 'INTERVAL 1 DAY':
                    $num_status=Departamentos::find($idDepartamento)
                    ->solicitudes()
                    ->select(DB::raw("count(*) as total, DATE_FORMAT(solicitud.fecha_creacion,'%k Horas') as hora"))
                    ->whereRaw("date(solicitud.fecha_creacion) >= (now() - $rangoTiempo)")
                    ->groupBy('hora')
                    ->get();
                    return $num_status;
                break;
                case 'INTERVAL 7 DAY':
                    $num_status=Departamentos::find($idDepartamento)
                    ->solicitudes()
                    ->select(DB::raw("count(*) as total, DATE_FORMAT(date(solicitud.fecha_creacion),'%e-%m-%Y') as fecha"))
                    ->whereRaw("date(solicitud.fecha_creacion) >= (now() - $rangoTiempo)")
                    ->groupBy('fecha')
                    ->get();
                    return $num_status;
                break;
                case 'INTERVAL 1 MONTH':
                    $num_status=Departamentos::find($idDepartamento)
                    ->solicitudes()
                    ->select(DB::raw("count(*) as total, DATE_FORMAT(date(solicitud.fecha_creacion),'%e-%m-%Y') as fecha"))
                    ->whereRaw("date(solicitud.fecha_creacion) >= (now() - $rangoTiempo)")
                    ->groupBy('fecha')
                    ->get();
                    return $num_status;
                break;
                case 'INTERVAL 3 MONTH':
                    DB::statement("SET lc_time_names = 'es_ES'");
                    $num_status=Departamentos::find($idDepartamento)
                    ->solicitudes()
                    ->select(DB::raw("count(*) as total, DATE_FORMAT(date(solicitud.fecha_creacion),'%M - %Y') as mes"))
                    ->whereRaw("date(solicitud.fecha_creacion) >= (now() - $rangoTiempo)")
                    ->groupBy("mes")
                    ->get();
                    return $num_status;
                break;
            }
           
            
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' => $rangoTiempo
            ]);
        }
    }
    public function get_num_solicitudes_through_time_cerradas(Request $request){
        $idUsuario=Session::get('id_sgu');
        $rangoTiempo=$request->input('rangoTiempo');
        $idDepartamento=Session::get('id_departamento');
        
        try{
            switch($rangoTiempo){
                case 'INTERVAL 1 DAY':
                    $num_status=Departamentos::find($idDepartamento)
                    ->solicitudes()
                    ->select(DB::raw("count(*) as total, DATE_FORMAT(solicitud.fecha_creacion,'%k Horas') as hora"))
                    ->where('estatus','Cerrada')
                    ->whereRaw("date(solicitud.fecha_creacion) >= (now() - $rangoTiempo)")
                    ->groupBy('hora')
                    ->get();
                    return $num_status;
                break;
                case 'INTERVAL 7 DAY':
                    $num_status=Departamentos::find($idDepartamento)
                    ->solicitudes()
                    ->select(DB::raw("count(*) as total, DATE_FORMAT(date(solicitud.fecha_creacion),'%e-%m-%Y') as fecha"))
                    ->where('estatus','Cerrada')
                    ->whereRaw("date(solicitud.fecha_creacion) >= (now() - $rangoTiempo)")
                    ->groupBy('fecha')
                    ->get();
                    return $num_status;
                break;
                case 'INTERVAL 1 MONTH':
                    $num_status=Departamentos::find($idDepartamento)
                    ->solicitudes()
                    ->select(DB::raw("count(*) as total, DATE_FORMAT(date(solicitud.fecha_creacion),'%e-%m-%Y') as fecha"))
                    ->where('estatus','Cerrada')
                    ->whereRaw("date(solicitud.fecha_creacion) >= (now() - $rangoTiempo)")
                    ->groupBy('fecha')
                    ->get();
                    return $num_status;
                break;
                case 'INTERVAL 3 MONTH':
                    DB::statement("SET lc_time_names = 'es_ES'");
                    $num_status=Departamentos::find($idDepartamento)
                    ->solicitudes()
                    ->select(DB::raw("count(*) as total, DATE_FORMAT(date(solicitud.fecha_creacion),'%M - %Y') as mes"))
                    ->where('estatus','Cerrada')
                    ->whereRaw("date(solicitud.fecha_creacion) >= (now() - $rangoTiempo)")
                    ->groupBy("mes")
                    ->get();
                    return $num_status;
                break;
            }
            
            
            
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' => $rangoTiempo
            ]);
        }
    }
    public function get_num_solicitudes_by_estatus_usuario(Request $request){
        
        
        try{
            
            $idUsuario=$request->input('idUsuario');
            if($idUsuario!=''){
                $num_status=Usuario::find($idUsuario)
                ->solicitudes()
                ->select('solicitud.estatus',DB::raw('count(*) as total'))
                ->groupBy('solicitud.estatus')->orderBy('total','DESC')->get();
                
                return $num_status;
            }
            else{
                return response()->json([
                    'status' => false,
                    'data' => ''
                ]);
            }
            
            
            
            
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' => $idUsuario
            ]);
        }
    }
    public function get_usuarios_by_departamento(){
        $idDepartamento=Session::get('id_departamento');
        
        try{
            $usuarios=Departamentos::find($idDepartamento)
            ->usuarios()
            ->where('usuario.id_sgu','!=','1') 
            ->get();
            
           
            foreach($usuarios as $u)
            {
                if(!is_null($u->id_sgu))
                {
                    $u->nombre = mb_strtoupper($this->get_usuario($u->id_sgu)['nombre']);
                }
                
            }
            
            
            return $usuarios;
            
            
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' => $idDepartamento
            ]);
        }
    }
    public function insert(Request $request){

        //Insert a tabla Solicitud
        $solicitud=new Solicitud;
        $solicitud->id_usuario= Session::get('id_sgu');;
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
    public function get_solicitudes_departamento_rep(Request $request){
        $busqueda=$request->input('busqueda');
        $page=$request->input('page');
        $num=$request->input('num');
        $medio=$request->input('medio');
        $estado=$request->input('estado');
        $id_solicitud=$request->input('id_solicitud');
        $idDep=Session::get('id_departamento');
        $orden=$request->input('orden');
        
        
        try{
            $solicitudes_dep=Departamentos::find($idDep)->solicitudes()
                ->where('solicitud.id_solicitud','like',"%$id_solicitud%")
                ->where('solicitud.descripcion','like',"%$busqueda%")
                ->where('solicitud.medio_reporte','like',"%$medio%")
                ->where('solicitud.estatus','like',"$estado")
                ->with('usuario_many')
                ->orderBy('solicitud.id_solicitud',$orden)
            ->get();
            return $solicitudes_dep;
        }catch(Exception $e){
            return response()->json([
                'status' => false,
                'data' =>''
            ]);
        }
    }
    public function get_porcentaje_cerradas()
    {
        $idDep=Session::get('id_departamento');
        $solicitudes=Departamentos::find($idDep)
        ->solicitudes()
        ->get();
        $solicitudes_close=Departamentos::find($idDep)
        ->solicitudes()
        ->where('solicitud.estatus',"Cerrada")
        ->get();
        return $porcentaje = ($solicitudes_close->count() / $solicitudes->count()) * 100;
        
    }
}

