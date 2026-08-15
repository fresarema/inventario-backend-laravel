<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioConteo extends Model
{
    use HasFactory;

    protected $table = 'inventario_conteo';
    protected $guarded = [];

    // Relación con el producto maestro si se requiere
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'codigo_producto', 'codigo');
    }
}