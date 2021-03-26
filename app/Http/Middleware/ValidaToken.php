<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;
use Redirect;
use App\Models\Usuario;
use App\Models\Departamentos;
class ValidaToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //Session::flush();
        if(Session::get('key')){
            return $next($request);
        }
        $token = $request->query('ssid');
       
        Session::put([
            'key'=>$token,
            'id_sgu'=>'1865',
            'nombre'=> 'Kevin Llamas',
            'curp'=> 'MAHL920209HJCRRS02',
            'usuario'=> 'kevindejesus.llamas@jalisco.gob.mx',
            'rol'=>'SUPER',
            'id_departamento'=>2,
            'departamento'=>'DTI',
        ]);
        Session::save();
        return $next($request);
        
    }
    /* $token = $request->query('ssid');
        
        //Se esta ingresando a cualquier url sin el token
        if(is_null($token)){
            
            if(is_null(Session::get("key"))){
                //Redireccionamos
                $url = "http://mi.sej.jalisco.gob.mx?servicio=https://plataformadigital.sej.jalisco.gob.mx/sass/";
                //$url="http://google.com";
                return Redirect::to($url);

            }
            else{
                
                $sesion_local = Session::get("key");
                $res = self::validar($sesion_local);
                
                
                if($res->ok === true){
                     return $next($request);
                }
                else{

                    //Matamos sesión local y redireccionamos
                    Session::flush();
                    $url = "http://mi.sej.jalisco.gob.mx?servicio=https://plataformadigital.sej.jalisco.gob.mx/sass/";
                    return Redirect::to($url);
                }
            }

        }
        //Se ingresa con el token en la url
        else{

            $res = self::validar($token);
            
            if($res->ok === true){
                //Data SGU
                $result = self::buscar_usuario($token);
                //Rol app
                $usuario = Usuario::where('id_sgu',$res->id_persona)->first();
                $departamento = Departamentos::find($usuario->id_departamento);

                    Session::put([
                        'key'=>$token,
                        'id_sgu'=>$res->id_persona,
                        'nombre'=> $result["nombre"],
                        'curp'=> $result["curp"],
                        'usuario'=> $result["usuario"],
                        'rol'=>$usuario->rol,
                        'id_departamento'=>$departamento->id,
                        'departamento'=>$departamento->nombre,
                    ]);
                    Session::save();
                    return $next($request);
                
            }
            else{
                //Matamos sesión local y redireccionamos
                Session::flush();
                $url = "http://mi.sej.jalisco.gob.mx?servicio=https://plataformadigital.sej.jalisco.gob.mx/sass/";
                return Redirect::to($url);
            }
        }
    }
    public function buscar_usuario($token) {
        try {

            $auth = $token;
            $url = curl_init("http://10.9.4.152:3000/getNombrePersona");

            //Token enviado por cabecera http
            $headers = array('auth:'. $auth);
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            //establecemos el verbo http que queremos utilizar para la petición
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($url, CURLOPT_HTTPHEADER, $headers);
            //curl_setopt($url, CURLOPT_POSTFIELDS,http_build_query($data));
            $response = curl_exec($url);
            // Se cierra el recurso CURL y se liberan los recursos del sistema
            curl_close($url);
            return $res = json_decode($response, true);
        } catch (Exception $e) {
            return response()->json([
                'status' => FALSE,
                'data' => ''
            ], 500);
        }
    }
    public function validar($token){

        $url = curl_init("http://10.9.4.152:3000/sesionValida");

        //Token enviado por cabecera http
        $headers = array('auth:'. $token);
        $llaveApp = "B0342DEF578109AD4C32E158B2702E884645493F84A0AFACA05A017D3E68D3F8";
        $data = array(
            "llaveApp" => $llaveApp
        );
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        //establecemos el verbo http que queremos utilizar para la petición
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($url, CURLOPT_POSTFIELDS,http_build_query($data));
        $response = curl_exec($url);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($url);

        $res = json_decode($response);
        return $res;
    } */
}
