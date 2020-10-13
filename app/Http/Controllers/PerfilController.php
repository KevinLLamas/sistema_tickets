<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Perfil;

class PerfilController extends Controller
{
    function perfiles(){
        $perfiles = Perfil::all();
        return $perfiles;
    }
}
