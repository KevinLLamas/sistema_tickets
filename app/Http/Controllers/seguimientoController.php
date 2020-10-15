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


class seguimientoController extends Controller {
	public function seguimiento($id){
		//$categoria = Categoria::all();
		//$categoria = Solicitud::with(['subcategoria','atencion'])->find($id);
		return Solicitud::with(['subcategoria','atencion','usuario', 'dato_adicional'])->where('id_solicitud', $id)->first();
	}
}