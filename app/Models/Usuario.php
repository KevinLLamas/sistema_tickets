<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function solicitudes(){
        return $this->belongsToMany('App\Models\Solicitud','solicitud_usuario','id_usuario','id_solicitud');
        //return $this->belongsToMany('Tabla relacionada','Tabla_intermedia','id Tabla actual(tabla intermedia)','id Tabla relacionada(tabla intermedia)');
    }
    public function departamento(){
        return $this->hasOne('App\Models\Departamentos','id','id_departamento');
    }

}