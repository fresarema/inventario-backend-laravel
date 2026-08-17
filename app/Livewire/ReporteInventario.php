<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RegistroConteo;
use App\Models\Sucursal;
use App\Models\Inventario;
use App\Models\InventarioConteo; // Aseguramos la importación del modelo de conteo

class ReporteInventario extends Component
{
    // Variables enlazadas a los inputs[cite: 3]
    public $sucursalId = '';
    public $inventarioId = ''; 
    public $metro = '';

    // "Hook" de Livewire: Se ejecuta automáticamente cuando el usuario cambia la sucursal[cite: 3]
    public function updatedSucursalId()
    {
        // Si cambia de local, reseteamos el inventario y el metro para evitar cruces de datos[cite: 3]
        $this->inventarioId = '';
        $this->metro = '';
    }

    public function render()
    {
        // 1. Cálculo de Métricas (KPIs) en tiempo real
        $inventariosActivos = Inventario::where('estado', 1)->count();
        $localesEnProceso = Inventario::where('estado', 1)->distinct('codLocal')->count('codLocal');
        $ultimaSincronizacion = InventarioConteo::max('created_at');

        // Extrae los locales únicos directamente de la tabla Inventario[cite: 3]
        $sucursales = Inventario::select('codLocal', 'nombre_local')
                                ->distinct()
                                ->get();
        
        // Carga los inventarios según el local seleccionado (codLocal)[cite: 3]
        $inventarios = collect();
        if ($this->sucursalId) {
            $inventarios = Inventario::where('codLocal', $this->sucursalId)
                                     ->orderBy('id', 'desc')
                                     ->get();
        }

        // Cargar los registros usando la tabla 'inventario_conteo'[cite: 3]
        $registros = collect();
        
        if ($this->inventarioId) {
            $registros = InventarioConteo::leftJoin('metros', 'inventario_conteo.metro_id', '=', 'metros.id')
                ->select('inventario_conteo.*', 'metros.numeroMetro as nombre_metro')
                ->where('inventario_conteo.inventario_id', $this->inventarioId)
                ->when($this->metro, function($query) {
                    // Especificamos la tabla para evitar ambigüedad en el WHERE
                    $query->where('metros.numeroMetro', $this->metro);
                })
                ->get();
        }

        return view('livewire.reporte-inventario', compact(
            'sucursales', 
            'inventarios', 
            'registros',
            'inventariosActivos',
            'localesEnProceso',
            'ultimaSincronizacion'
        ));
    }

    public function limpiarFiltros()
    {
        $this->metro = ''; //[cite: 3]
    }
}