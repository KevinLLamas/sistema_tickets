<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Departamentos extends Model
{
    protected $table = 'departamento';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function solicitudes(){
        return $this->belongsToMany('App\Models\Solicitud','solicitud_departamento','id_departamento','id_solicitud');
    }
    public function usuarios(){
        return $this->hasMany('App\Models\Usuario', 'id_departamento', 'id');
    }
    public function subcategorias(){
        return $this->belongsToMany('App\Models\Subcategoria','subcategoria_departamento','id_departamento','id_subcategoria');
    }

} 