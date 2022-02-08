<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Usuario_login extends Model
{
    protected $table = 'usuario_login';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;
    public function usuario_info(){
    	return $this->hasOne('App\Models\Usuario','id','id_usuario');
    }
} 