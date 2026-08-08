<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'carreras';
    protected $primarykey = 'id';
    protected $keytype = 'integer';

    protected $fillable = [
        'id',
        'nombre',
        'jefe_carrera',
    ];
}
