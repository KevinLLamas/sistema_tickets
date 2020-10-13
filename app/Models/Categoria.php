<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Categoria extends Model
{
    protected $table = 'categoria';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function subcategorias()
    {
        return $this->hasManyThrough(
            '\App\Models\Subcategoria', //Modelo destino
            '\App\Models\Categoria_subcategoria', //Modelo intermedio
            'id_categoria', //Clave foranea en la tabla intermedia
            'id', //Clave foranea en la tabla de destino
            'id', //Clave primaria en la tabla origen 
            'id_subcategoria' //Clave primaria en la tabla intermedia
        );
    }
} 