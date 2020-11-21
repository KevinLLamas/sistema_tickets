<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subcategoria_departamento extends Model
{
    protected $table = 'subcategoria_departamento';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function departamentos(){
        return $this->hasMany('App\Models\Departamentos','id', 'id_departamento');
    }
}