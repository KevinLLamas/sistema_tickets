<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Solicitud_notificacion extends Model
{
    protected $table = 'solicitud_notificacion';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function usuario(){
    	return $this->hasOne('App\Models\Usuario', 'id', 'id_usuario');
    }
    public function atencion(){
    	return $this->hasOne('App\Models\Solicitud_atencion', 'id', 'id_atencion');
    }
}