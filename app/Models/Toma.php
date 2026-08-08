<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toma extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'tomas';
    protected $primarykey = 'id';
    protected $keytype = 'integer';

    protected $fillable = [
        'id',
        'estudiantes_run',
        'docentes_run',
        'asignaturas_id',
        'año',
        'semestre',
        'estado',
    ];
}
