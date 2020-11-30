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
use App\Models\Solicitud_departamento;
use App\Models\Solicitud_usuario;
use App\Models\Atencion_externos;
use App\Models\Atencion_adjunto;
use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\Departamentos;
use App\Models\Subcategoria_departamento;
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;
use App\Models\Solicitud_notificacion;
class seguimientoController extends Controller 
{
	protected $users = array();
	private function get_usuario($id){
		//LOCAL
		if($id == null || $id == 0)
			return ['ok'=>false,'nombre'=>'Usuario'];
		foreach ($this->users as $user) {
			if($user['id_sgu'] == $id)
				return $user;
		}
		//API
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
		array_push($this->users, ['ok'=>true,'id_sgu'=>$id,'nombre'=>$res['nombre']]);
		return $res;
	}
	public function getUsers()
	{
		return $this->users;
	}
	public function seguimiento($id){
		$departamentos_seleccionados_id = array();
		$solicitud = Solicitud::with(['subcategoria_departamento','subcategoria','atencion','usuario', 'dato_adicional', 'departamento', 'solicitud_usuario'])->where('id_solicitud', $id)->first();
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
						//$at->correo_usuario = $res['usuario'];
					}
				}
				else{
					if($at->tipo_at == 'Asignacion' ||$at->tipo_at == 'Estatus')
					{
						$at->nombre = 'Sistema';
						$at->correo_usuario = 'Sistema';
					}
					else if($at->tipo_at == 'Atención')
					{
						$at->nombre = 'Usuario';
						$at->correo_usuario = 'Usuario';
					}
				}
				$at->adjuntos = $adjuntos;
				
			}
		}
		$departamento_seleccionado_id = '';
		$departamentos = $solicitud->departamento;
		foreach($departamentos as $dtp)
		{
			$usuarios = Usuario::where('id_departamento', $dtp->id)->get();
			$dtp->integrantes = $usuarios;
			foreach($dtp->integrantes as $usuario)
			{
				$usuario->nombre = $this->get_usuario($usuario->id_sgu)['nombre'];
			}
			$departamentos_seleccionados_id[] = $dtp->id;
		}
		$solicitud->departamentos_seleccionados_id = $departamentos_seleccionados_id;
		$solicitud->categoria = Categoria::find($solicitud->id_categoria);
		$solicitud->perfil = Perfil::find($solicitud->id_perfil);
		$subcategoria_departamento = $solicitud->subcategoria_departamento;
		foreach($subcategoria_departamento as $sub_dpto)
		{
			$dep = Departamentos::find($sub_dpto->id_departamento);
			$sub_dpto->nombre_departamento = $dep->nombre;
		}
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
		$primer = false;
		if($request->input('estatus') != "Atendiendo" && $Sol_atencion['tipo_respuesta'] == 'Todos' && $atencion->tipo_at == 'Atencion')
			$primer = $this->ContarAtenciones($atencion->id_solicitud);

		//NOTIFICACION
		$usuarios = Solicitud_usuario::where('id_solicitud',$atencion->id_solicitud)->get();
		foreach ($usuarios as $usuario) {
			if($usuario->id_usuario != Session::get('id_sgu')){
				$notificacion = new Solicitud_notificacion;
				$notificacion->id_solicitud = $atencion->id_solicitud;
				$notificacion->id_atencion = $atencion->id;
				$notificacion->id_usuario = $usuario->id_usuario;
				$notificacion->status = 'No leida';
				$notificacion->save();
			}
		}

		//EMAIL
		if($request->input('rol') != 'USUARIO' && $atencion->tipo_at == 'Atencion' && $Sol_atencion['tipo_respuesta'] == 'Todos')
		{
			if($this->send_mail_nueva($request->input('email'),$Sol_atencion['id_solicitud'],$Sol_atencion['detalle']) == 'Enviado');
				return response()->json([
					'ok'=> true,
					'primer' => $primer, 
					'id' =>$atencion->id,
					'message'=>'Se envio el correo.'
				]);
		}
		return response()->json([
			'ok'=> true,
            'primer' => $primer, 
			'id' =>$atencion->id,
			'message'=>'No se pudo enviar el correo.'
        ]);
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
		if($Sol_atencion['fecha_finalizado'] != '')
			$atencion->momento = $Sol_atencion['fecha_finalizado'];
		$atencion->tipo_respuesta = $Sol_atencion['tipo_respuesta'];
		$atencion->save();

		//NOTIFICACION
		$usuarios = Solicitud_usuario::where('id_solicitud',$atencion->id_solicitud)->get();
		
		foreach ($usuarios as $usuario) {
			$notificacion = new Solicitud_notificacion;
			$notificacion->id_solicitud = $atencion->id_solicitud;
			$notificacion->id_atencion = $atencion->id;
			$notificacion->id_usuario = $usuario->id_usuario;
			$notificacion->status = 'No leida';
			$notificacion->save();
		}
		return $atencion->id;
	}
	
	public function cambiar_estatus(Request $request)
	{
		$id = $request->input('id');
		$solicitud = Solicitud::find($id);
		$solicitud->estatus =  $request->input('estatus');
		if($solicitud->estatus == 'Cerrada')
			$solicitud->fecha_finalizado = now();
		else
		$solicitud->fecha_finalizado = null;
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
		//$solicitud = Solicitud::with(['subcategoria','atencion','usuario', 'dato_adicional', 'departamento', 'solicitud_usuario'])->where('id_solicitud', $id)->first();

		$fuera = array();
		$dentro = array();

		$integrantes_seleccionados = $request->input('integrantes');
		$id_solicitud = $request->input('id_solicitud');	
		$id_departamento = $request->input('id_departamento');		
		
		//Desactivar integrantes no seleccionados que existen	
		//$integrantes = Solicitud_usuario::where('id_solicitud',$id_solicitud)->where('estado', 'Atendiendo')->get();
		$integrantes = Solicitud_usuario::with(['usuario'])->where('id_solicitud',$id_solicitud)->where('estado', 'Atendiendo')->get();
		if(count($integrantes_seleccionados) != 0)	
		{			
			if(count($integrantes) != 0)
			{
				foreach($integrantes as $integ)
				{
					if($integ->usuario->id_departamento == $id_departamento)
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
			}
		}
		else if(is_numeric($integrantes))
		{
            if($integrantes != 0)
            {
                foreach($integrantes as $integ)
                {
                    if($integ->usuario->id_departamento == $id_departamento)
                    {
                        $integ->estado = 'Suspendido';
                        $integ->save();	
                    }
                }
            }
		}

		//Reactivar usuario en una solicitud o agregarlo si no existe
		$integrantes = Solicitud_usuario::where('id_solicitud',$id_solicitud)->whereIn('id_usuario', $integrantes_seleccionados)->get();
		$ban = false;
		if(count($integrantes) != 0)
		{
			foreach($integrantes_seleccionados as $integ_sel)
			{
				foreach($integrantes as $integ)
				{
					if($integ->id_usuario == $integ_sel)
					{
						$ban = true;
						$integ->estado = 'Atendiendo';
						$integ->momento = now();
						$integ->save();
						$clave = array_search($integ_sel, $integrantes_seleccionados);
						unset($integrantes_seleccionados[$clave]);
						break;
					}
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
		$at = Atencion_externos::where('id_solicitud', $id_solicitud)->first();//->Esta linea se agrega
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
            $mail->setFrom("noreplay@jaliscoedu.mx", 'SASS');
            $mail->CharSet = 'UTF-8';
            $mail->addAddress(trim($email));

            $mail->Subject = "Contestación en Ticket levantado.";
            $mail->isHTML(true);
            $headers = "Content-Type: text/html; charset=UTF-8";
            $mailContent = "
					<p>Te han contestado en el ticket #$id_solicitud el sistema SAS.</p>
					<p>Respuesta: $detalle </p>
                    <p>Para dar seguimiento a su ticket, <a href='https://plataformadigital.sej.jalisco.gob.mx/sass/seguimiento_externo/$at->solicitud'>por favor ingrese a este enlace.</a></p>
                    <p>Código: $at->codigo</p>
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
	
	public function ContarAtenciones($id)
	{
		$num = Solicitud_atencion::where('id_solicitud', $id)->where('tipo_respuesta', 'Todos')->where('tipo_at', 'Atencion')->count();
		if($num == 1)
		{
			return true;
		}
		return false;
	}

	public function UpdateDepartamentos(Request $request)
	{
		$ban = false;
		$id_solicitud = $request->input('id_solicitud');	
		$id_departamentos = $request->input('id_departamentos');	

		Solicitud_departamento::where('id_solicitud', $id_solicitud)->delete();
		
		foreach($id_departamentos as $id_departamento)
		{
			$solicitud_departamento = new Solicitud_departamento;
			$solicitud_departamento->id_solicitud = $id_solicitud;
			$solicitud_departamento->id_departamento = $id_departamento;
			$solicitud_departamento->aceptada = 'true';
			$solicitud_departamento->razon = '';
			$solicitud_departamento->save();
		
		
			$integrantes = Solicitud_usuario::with(['usuario'])->where('id_solicitud',$id_solicitud)->where('estado', 'Atendiendo')->get();			
			if(count($integrantes) != 0)
			{
				foreach($integrantes as $integ)
				{
					if($integ->usuario->id_departamento == $id_departamento)
					{
						$ban = true;
					}
				}		
			}

			if(!$ban)
			{
				//TRAEMOS TECNICOS DE EL DEPARTAMENTO ACTUAL
				$usuarios_depa = Usuario::with('ultima_asignada')
				->where('id_departamento', $id_departamento)
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

				$atencion = new Solicitud_atencion;

				$nombre = '';
				$res = $this->get_usuario($usuario_asignar->id_sgu);
				if($res['ok'])
					$nombre = $res['nombre'];
				else
					$nombre = 'Usuario';
				$atencion->detalle = 'asignó a ' . $nombre . ' a este ticket.';
				$atencion->id_solicitud = $id_solicitud;
				$atencion->tipo_at = 'Asignacion';
				$atencion->momento = now();
				$atencion->tipo_respuesta = 'Todos';
				$atencion->save();
				return $atencion;
			}
		}
		return true;
	}

	function Subcategorias_Dptos(Request $request)
	{
		$subcategoria_departamento = Subcategoria_departamento::where('id_subcategoria', $request->input('id_subcategoria'))->get();
		foreach($subcategoria_departamento as $sub_dpto)
		{
			$dep = Departamentos::find($sub_dpto->id_departamento);
			$sub_dpto->nombre_departamento = $dep->nombre;
		}
		return $subcategoria_departamento;
	}

	public function UpdateSubcategoria(Request $request)
	{
		$id_solicitud = $request->input('id_solicitud');	
		$id_subcategoria = $request->input('id_subcategoria');	
		$id_categoria = $request->input('id_categoria');
		$solicitud = Solicitud::find($id_solicitud);
		$solicitud->id_subcategoria = $id_subcategoria;
		if($solicitud->id_categoria != $id_categoria)
			$solicitud->id_categoria = $id_categoria;			
		$solicitud->save();
		return true;
	}
}