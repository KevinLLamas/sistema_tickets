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
        Session::put([
            "id" => 1,
            "usuario" => "kevin@gmail.com",
            "nombre" => "Kevin Llamas Alcalá",
            "path_foto" => "storage/profile/5213313309193.png",
            "numero" => "5213313309193",
            "id_perfil" => 1,
            "rol" => "SUPER",
            "id_departamento" => 1
        ]);
        Session::save();
        return $next($request);
        /*
        if(is_null(Session::get("id"))){
            Session::flush();
            return Redirect::to('/');
        }
        else{
            return $next($request);
        }*/
    }
}
