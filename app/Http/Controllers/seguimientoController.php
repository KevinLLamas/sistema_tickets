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
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

class seguimientoController extends Controller 
{
	public function seguimiento($id){
		$solicitud = Solicitud::with(['subcategoria','atencion','usuario', 'dato_adicional', 'departamento', 'solicitud_usuario'])->where('id_solicitud', $id)->first();
		if($solicitud->atencion != null)
		{
			if(!is_null($solicitud->usuario))
			{
				$res = $this->get_usuario($solicitud->usuario->id_sgu);
				if($res['ok'])
					$solicitud->usuario->nombre = $res['nombre'];
				else
					$solicitud->usuario->nombre = 'Usuario';
			}
			else
				$solicitud->usuario = (object) ['nombre'=>'Usuario'];
			$atencion = $solicitud->atencion;
			foreach($atencion as $at)
			{
				$adjuntos = Atencion_adjunto::where('id_atencion', $at->id)->get();
				if($at->id_usuario != null)
				{
					$res = $this->get_usuario($at->id_usuario);
					if($res['ok']){
						$at->nombre = $res['nombre'];
						$at->correo_usuario = $res['usuario'];
					}
				}
				else{
					$at->nombre = 'Usuario';
					$at->correo_usuario = 'Usuario';
				}
				$at->adjuntos = $adjuntos;
				
			}
		}
		$departamentos = $solicitud->departamento;
		foreach($departamentos as $dtp)
		{
			$usuarios = Usuario::where('id_departamento', $dtp->id)->get();
			$dtp->integrantes = $usuarios;
			foreach($dtp->integrantes as $usuario)
			{
				$usuario->nombre = $this->get_usuario($usuario->id_sgu)['nombre'];
			}
		}
		return $solicitud;
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
		if($request->input('rol') != 'USUARIO' && $atencion->tipo_at == 'Atencion' && $Sol_atencion['tipo_respuesta'] == 'Todos')
		{
            if($this->send_mail_nueva($request->input('email'),$Sol_atencion['id_solicitud'],$Sol_atencion['detalle']) == 'Enviado');
                return $atencion->id;
		}
		return $atencion->id;
	}

	public function inserta_atencion_externo(Request $request){
		$atencion = new Solicitud_atencion;
		$Sol_atencion = $request->input('data');	
			
		$atencion->detalle = $Sol_atencion['detalle'];
		$atencion->id_solicitud = $Sol_atencion['id_solicitud'];
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
                'id_solicitud' =>$this->desecriptar($id),
            ]);
		}
		else
		{
			return response()->json([
                'status' => false,
            ]);
		}
	}
	private function desecriptar($texto)
    {
        $newEncrypter = new \Illuminate\Encryption\Encrypter(base64_decode('CcUAOtSqoNvtEfMKG3FmhsOQIBiiDYL7ZQxppYG82WI='), "AES-256-CBC" );
        return $decrypted = $newEncrypter->decrypt($texto);
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
		$fuera = array();
		$dentro = array();

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
	private function encriptar($texto)
    {
        $newEncrypter = new \Illuminate\Encryption\Encrypter(base64_decode('CcUAOtSqoNvtEfMKG3FmhsOQIBiiDYL7ZQxppYG82WI='), "AES-256-CBC" );
        return $encrypted = $newEncrypter->encrypt($texto);
    }
    private function desencriptar($texto)
    {
        $newEncrypter = new \Illuminate\Encryption\Encrypter(base64_decode('CcUAOtSqoNvtEfMKG3FmhsOQIBiiDYL7ZQxppYG82WI='), "AES-256-CBC" );
        return $decrypted = $newEncrypter->decrypt($texto);
    }
	public function send_mail_nueva($email,$id_solicitud, $detalle){
		$direccion = $this->encriptar($id_solicitud);
        $mail = new PHPMailer(true);
        try{
            $mail->isSMTP();
            $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'AKIATCA5M63WVFEMVFE3';
            $mail->Password = 'BG9yGrkHgndFSF0aJcQv1L8fFj9k+jnjHigMmpkkUSMA';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom("noreplay@jaliscoedu.mx", 'SAS');
            $mail->CharSet = 'UTF-8';
            $mail->addAddress(trim($email));

            $mail->Subject = "Confirmación de ticket creado.";
            $mail->isHTML(true);
            $headers = "Content-Type: text/html; charset=UTF-8";
            $mailContent = "
					<p>Te han contestado en el ticket #$id_solicitud el sistema SAS.</p>
					<p>Respuesta: $detalle </p>
                    <p>Para dar seguimiento a su ticket, <a href='https://plataformadigital.sej.jalisco.gob.mx/sass/seguimiento_externo/$direccion'>por favor ingrese a este enlace.</a></p>
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
}