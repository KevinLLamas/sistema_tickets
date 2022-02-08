<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Usuario_login;
use Redirect;
class LoginController extends Controller
{
    public function login(Request $request) {
        $user = $request->input("user");
        $password = hash('SHA512', $request->input("pass"));
        $usuario = Usuario_login::with('usuario_info')->where('usuario', $user)->where('password', $password)->first();
        if($usuario){
            Session::put([
                'id'=>$usuario->usuario_info->id,
                'usuario'=>$usuario->usuario,
                'nombre'=>$usuario->usuario_info->nombre,
                'path_foto'=>$usuario->usuario_info->path_foto,
                'numero'=>$usuario->usuario_info->numero,
                'id_perfil'=>$usuario->usuario_info->id_perfil,
                'rol'=>$usuario->usuario_info->rol,
                'id_departamento'=>$usuario->usuario_info->id_departamento,
            ]);
            Session::save();
            return response()->json([
                'ok' => true,
                'message' => ''
            ], 200);
        }
        else{
            return response()->json([
                'ok' => false,
                'message' => 'Credenciales incorrectas, favor de corroborar su información'
            ], 200);

        }
    }
    public function logout() {
        Session::flush();
        return redirect('/');
    }
}