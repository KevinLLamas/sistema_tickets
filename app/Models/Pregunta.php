<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Pregunta extends Model
{
    protected $table = 'pregunta';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public function opciones(){
    	return $this->hasMany('App\Models\Opciones', 'id_pregunta');
    }
}