<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function logout() {

        $url = curl_init("http://10.9.4.152:3000/logout");
        //Token enviado por cabecera http
        $auth = Session::get("key");
        $headers = array('auth:'. $auth);
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($url);
        curl_close($url);

        header('Content-Type: application/json');
        $res = json_decode($response);
        if( $res->ok == true ){
            Session::flush();
            return redirect('http://mi.sej.jalisco.gob.mx?servicio=https://plataformadigital.sej.jalisco.gob.mx/sass/');
        }
        else{
            Session::flush();
            return redirect('http://mi.sej.jalisco.gob.mx?servicio=https://plataformadigital.sej.jalisco.gob.mx/sass/');
        }
    }
}