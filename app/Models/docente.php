<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class docente extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'docentes';
    protected $primarykey = 'run_d';
    protected $keytype = 'integer';

    protected $fillable = [
        'run_d',
        'nombres_d',
        'apellidos_d',
        'educacion_d',

    ];
}
