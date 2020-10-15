<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Solicitud extends Model
{
    protected $table = 'solicitud';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

    public function usuario(){
        return $this->belongsToMany('App\Models\Usuario','Solicitud_usuario','id_solicitud','id_usuario')
        ->using('App\Models\Solicitud_usuario')
        ->withPivot(['momento','estado','razon','momento_fin']);
    }

}