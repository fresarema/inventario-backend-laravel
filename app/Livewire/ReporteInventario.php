<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RegistroConteo;
use App\Models\Sucursal;
use App\Models\Inventario;

class ReporteInventario extends Component
{
    // Variables enlazadas a los inputs
    public $sucursalId = '';
    public $inventarioId = ''; 
    public $metro = '';

    // "Hook" de Livewire: Se ejecuta automáticamente cuando el usuario cambia la sucursal
    public function updatedSucursalId()
    {
        // Si cambia de local, reseteamos el inventario y el metro para evitar cruces de datos
        $this->inventarioId = '';
        $this->metro = '';
    }

    public function render()
    {
        $sucursales = Sucursal::all();
        
        // 1. Cargar los inventarios SOLO si hay una sucursal seleccionada
        $inventarios = collect();
        if ($this->sucursalId) {
            $inventarios = Inventario::where('sucursal_id', $this->sucursalId)
                                     ->orderBy('id', 'desc') // Los más recientes primero
                                     ->get();
        }

        // 2. Cargar los registros SOLO si hay un inventario seleccionado
        $registros = collect();
        if ($this->inventarioId) {
            $registros = RegistroConteo::with('producto')
                ->where('inventario_id', $this->inventarioId)
                ->when($this->metro, function($query) {
                    $query->where('metro', $this->metro);
                })
                ->get();
        }

        return view('livewire.reporte-inventario', compact('sucursales', 'inventarios', 'registros'));
    }

    public function limpiarFiltros()
    {
        $this->metro = '';
    }
}