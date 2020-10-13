<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Subcategoria;

class SubcategoriaController extends Controller
{
    function subcategorias(Request $request){
        $subcategorias = Categoria::with('subcategorias')->find($request->input('id_categoria'))->subcategorias;
        return $subcategorias;
    } 
}
