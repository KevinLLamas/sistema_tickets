<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Perfil;

class CategoriaController extends Controller
{
    function categorias(Request $request){
        $categorias = Perfil::with('categorias')->find($request->input('id_perfil'))->categorias;
        return $categorias;
    }
    public function insert(Request $request)
    {
        $categoria = new Categoria();
        $categoria->nombre = $request->input('nombre');
        $categoria->activa = 'true';
        $categoria->save();

        $idCategoria = $categoria->id;
        return $idCategoria;
    }
    public function delete(Request $request)
    {        
        $id = $request->input('id');       
        $cat = Categoria::find($id);
        $cat->activa ='false';
        $cat->save();
    }
    public function editar(Request $request)
    {        
        $id = $request->input('id');   
        $nombre = $request->input('nombre');     
        $cat = Categoria::find($id);
        $cat->nombre =$nombre;
        $cat->save();
    }
}
