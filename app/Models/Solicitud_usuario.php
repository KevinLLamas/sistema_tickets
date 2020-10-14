<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
class Solicitud_usuario extends Pivot
{
    protected $table = 'Solicitud_usuario';
    protected $primaryKey = 'id';
    public $timestamps = false;

    /**public function usuario(){
        return $this->hasOne('App\Models\Usuario','id');
    }
    public function solicitud(){
        return $this->hasOne('App\Models\Solicitud','id_solicitud');
    }**/
}