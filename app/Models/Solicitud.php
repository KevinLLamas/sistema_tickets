<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Solicitud extends Model
{
    protected $table = 'solicitud';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

    public function usuario_many(){
        return $this->belongsToMany('App\Models\Usuario','Solicitud_usuario','id_solicitud','id_usuario')
        ->using('App\Models\Solicitud_usuario')
        ->withPivot(['momento','estado','razon','momento_fin']);
    }

    public function subcategoria(){
    	return $this->hasOne('App\Models\Subcategoria', 'id', 'id_subcategoria');
    }
    public function atencion(){
    	return $this->hasMany('App\Models\Solicitud_atencion', 'id_solicitud');
    }
    public function usuario(){
    	return $this->hasOne('App\Models\Usuario', 'id', 'id_usuario');
    }
    public function dato_adicional(){
    	return $this->hasMany('App\Models\Solicitud_dato_adicional', 'id_solicitud');
    }
    public function categoria(){
    	return $this->hasMany('App\Models\Categoria', 'id_solicitud');
    }
}