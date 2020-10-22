<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_sgu';
    public $timestamps = false;
    
    public function solicitudes(){
        return $this->belongsToMany('App\Models\Solicitud','solicitud_usuario','id_usuario','id_solicitud');
        //return $this->belongsToMany('Tabla relacionada','Tabla_intermedia','id Tabla actual(tabla intermedia)','id Tabla relacionada(tabla intermedia)');
    }
    /*public function solicitudes()
    {
        return $this->hasManyThrough(
            '\App\Models\Solicitud', //Modelo destino
            '\App\Models\Solicitud_usuario', //Modelo intermedio
            'id_usuario', //Clave foranea en la tabla intermedia
            'id', //Clave foranea en la tabla de destino
            'id', //Clave primaria en la tabla origen 
            'id_solicitud' //Clave primaria en la tabla intermedia
        );
    }*/
    public function departamento(){
        return $this->hasOne('App\Models\Departamentos','id','id_departamento');
    }

}