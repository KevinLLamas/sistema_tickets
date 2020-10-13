<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Campo_personalizado extends Model
{
    protected $table = 'campo_personalizado';
    protected $primaryKey = 'id_campo_personalizado';
    public $timestamps = false;
    public function opciones()
    {
        return $this->hasMany('\App\Models\campo_personalizado_opciones', 'id_campo_personalizado');
    }
} 