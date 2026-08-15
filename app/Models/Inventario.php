<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    // Apunta a la tabla corporativa exacta
    protected $table = 'Inventario';
    
    // Desactiva los timestamps si la tabla original no tiene created_at y updated_at
    public $timestamps = false; 

    protected $fillable = [
        'user_id',
        'inventario', 
        'codLocal',
        'fecha',
        'observacion',
        'estado',
        'nombre_local'
    ];
}