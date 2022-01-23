<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Subcategoria;

class SubcategoriaController extends Controller
{
    function subcategorias(Request $request){
        $subcategorias = Subcategoria::where('id_categoria',$request->input('id_categoria'))->get();
        return $subcategorias;
    } 
}
