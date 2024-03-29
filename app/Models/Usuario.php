<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    public function solicitudes_atendiendo(){
        return $this->belongsToMany('App\Models\Solicitud','solicitud_usuario','id_usuario','id_solicitud')->withPivot('estado')->wherePivotIn('estado',['Atendiendo']);
        //return $this->belongsToMany('Tabla relacionada','Tabla_intermedia','id Tabla actual(tabla intermedia)','id Tabla relacionada(tabla intermedia)');
    }
    public function solicitudes(){
        return $this->belongsToMany('App\Models\Solicitud','solicitud_usuario','id_usuario','id_solicitud');
        //return $this->belongsToMany('Tabla relacionada','Tabla_intermedia','id Tabla actual(tabla intermedia)','id Tabla relacionada(tabla intermedia)');
    }
    public function ultima_asignada()
    {
        return $this->hasOne('App\Models\Solicitud_usuario','id_usuario','id')->latest('momento');
    }
    public function ultima_asignada2()
    {
        return $this->hasOne('App\Models\Solicitud_usuario','id_usuario','id')->latest('momento');
    }
    public function departamento(){
        return $this->belongsTo('App\Models\Departamentos');
    }

}