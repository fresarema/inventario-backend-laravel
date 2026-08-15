<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaestroLocal extends Model
{
    use HasFactory;

    protected $table = 'maestro_locales';
    public $timestamps = false;

    protected $fillable = [
        'codLocal',
        'nombre_local'
    ];
}