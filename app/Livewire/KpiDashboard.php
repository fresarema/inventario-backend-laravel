<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Inventario;
use App\Models\InventarioConteo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KpiDashboard extends Component
{
    public function render()
    {
        // 1. Identifica al usuario y sus locales permitidos
        $userId = Auth::id();
        $localesAsignados = DB::table('user_sucursal')
                              ->where('user_id', $userId)
                              ->pluck('sucursal_id');

        // 2. Calcula las métricas respetando el filtro de seguridad
        $inventariosActivos = Inventario::where('estado', 1)
                                        ->whereIn('codLocal', $localesAsignados)
                                        ->count();
                                        
        $localesEnProceso = Inventario::where('estado', 1)
                                      ->whereIn('codLocal', $localesAsignados)
                                      ->distinct('codLocal')
                                      ->count('codLocal');
        
        $inventariosIds = Inventario::whereIn('codLocal', $localesAsignados)->pluck('id');
        $ultimaSincronizacion = InventarioConteo::whereIn('inventario_id', $inventariosIds)
                                                ->max('updated_at');

        return view('livewire.kpi-dashboard', compact(
            'inventariosActivos',
            'localesEnProceso',
            'ultimaSincronizacion'
        ));
    }
}