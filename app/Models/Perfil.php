<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Perfil extends Model
{
    protected $table = 'perfil';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function categorias()
    {
        return $this->hasManyThrough(
            '\App\Models\Categoria', //Modelo destino
            '\App\Models\Perfil_categoria', //Modelo intermedio
            'id_perfil', //Clave foranea en la tabla intermedia
            'id', //Clave foranea en la tabla de destino
            'id', //Clave primaria en la tabla origen 
            'id_categoria' //Clave primaria en la tabla intermedia
        );
    }
}