<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroConteo extends Model
{
    use HasFactory;

    protected $fillable = ['inventario_id', 'usuario_id', 'metro', 'producto_codigo', 'cantidad'];

    // Relación para traer la descripción del producto
    public function producto()
    {
        // Enlaza el 'producto_codigo' de esta tabla con el 'codigo' de la tabla Productos
        return $this->belongsTo(Producto::class, 'producto_codigo', 'codigo');
    }
}
