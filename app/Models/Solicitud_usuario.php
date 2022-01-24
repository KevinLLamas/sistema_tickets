<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Solicitud_usuario extends Model
{
    protected $table = 'solicitud_usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;


    public function usuario(){
        return $this->hasOne('App\Models\Usuario','id', 'id_usuario');
    }
    /*public function solicitud(){
        return $this->hasOne('App\Models\Solicitud','id_solicitud');
    }**/
}