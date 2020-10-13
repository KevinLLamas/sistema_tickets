<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Solicitud extends Model
{
    protected $table = 'solicitud';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;

}