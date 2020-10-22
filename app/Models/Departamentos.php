<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Departamentos extends Model
{
    protected $table = 'departamento';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function solicitudes(){
        return $this->belongsToMany('App\Models\Solicitud','solicitud_departamento','id_departamento','id_solicitud');
    }
} 