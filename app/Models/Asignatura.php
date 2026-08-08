<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'asignaturas';
    protected $primarykey = 'id';
    protected $keytype = 'integer';

    protected $fillable = [
        'id',
        'nombre_a',
        'carrera_id',
        'horas',
        'estado',
    ];
}
