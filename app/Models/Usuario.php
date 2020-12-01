<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_sgu';
    public $timestamps = false;
    
    public function solicitudes(){
        return $this->belongsToMany('App\Models\Solicitud','solicitud_usuario','id_usuario','id_solicitud')->withPivot('estado')->wherePivotIn('estado',['Atendiendo']);
        //return $this->belongsToMany('Tabla relacionada','Tabla_intermedia','id Tabla actual(tabla intermedia)','id Tabla relacionada(tabla intermedia)');
    }
    public function ultima_asignada()
    {
        return $this->hasOne('App\Models\Solicitud_usuario','id_usuario','id_sgu')->latest('momento');
    }

    public function departamento(){
        return $this->belongsTo('App\Models\Departamentos');
    }

}