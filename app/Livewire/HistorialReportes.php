<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inventario;

class HistorialReportes extends Component
{
    use WithPagination;
    
    // Usa el diseño de Bootstrap para la paginación
    protected $paginationTheme = 'bootstrap'; 

    public function render()
    {
        // Filtra donde el estado NO sea 1 (Abierto) para mostrar solo el historial Cerrado
        $inventariosCerrados = Inventario::where('estado', '!=', 1)
                                ->orderBy('id', 'desc')
                                ->paginate(10);

        return view('livewire.historial-reportes', [
            'inventarios' => $inventariosCerrados
        ]);
    }
}