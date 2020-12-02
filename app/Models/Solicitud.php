<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Solicitud extends Model
{
    protected $table = 'solicitud';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

    public function fecha_creacion_date(){
        return $this->fecha_creacion->format('Y.m.d');
    }
    public function usuario_many(){
        return $this->belongsToMany('App\Models\Usuario','solicitud_usuario','id_solicitud','id_usuario')->withPivot('estado')->wherePivotIn('estado',['Atendiendo']);
    }
    public function departamento(){
        return $this->belongsToMany('App\Models\Departamentos','solicitud_departamento','id_solicitud','id_departamento');
    }
    public function subcategoria(){
    	return $this->hasOne('App\Models\Subcategoria', 'id', 'id_subcategoria');
    }
    public function subcategoria_departamento(){
    	return $this->hasMany('App\Models\Subcategoria_departamento', 'id_subcategoria', 'id_subcategoria');
    }
    public function atencion(){
    	return $this->hasMany('App\Models\Solicitud_atencion', 'id_solicitud');
    }
    public function usuario(){
    	return $this->hasOne('App\Models\Usuario', 'id_sgu', 'id_usuario');
    }
    public function dato_adicional(){
    	return $this->hasMany('App\Models\Solicitud_dato_adicional', 'id_solicitud');
    }
    public function categoria(){
    	return $this->hasMany('App\Models\Categoria', 'id_solicitud');
    }
    public function solicitud_usuario(){
    	return $this->hasMany('App\Models\Solicitud_usuario', 'id_solicitud');
    }
}