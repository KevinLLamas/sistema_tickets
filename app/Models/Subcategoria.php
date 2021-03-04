<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Subcategoria extends Model
{
    protected $table = 'subcategoria';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function solicitudes(){
        return $this->hasMany('App\Models\Solicitud', 'id_subcategoria', 'id');
    }

}