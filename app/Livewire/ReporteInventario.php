<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sucursal;
use App\Models\Inventario;
use App\Models\InventarioConteo;
use App\Exports\ReporteInventarioExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReporteInventario extends Component
{
    // Variables de Filtros 
    public $sucursalId = '';
    public $inventarioId = ''; 
    public $metro = '';

    // Variables de edicion
    public $editandoId = null;
    public $nuevoConteo = 0;

    public function updatedSucursalId()
    {
        $this->inventarioId = '';
        $this->metro = '';
    }

    // --- INICIO LÓGICA DE EDICIÓN ---
    public function activarEdicion($id, $cantidadActual)
    {
        $this->editandoId = $id;
        $this->nuevoConteo = $cantidadActual;
    }

    public function cancelarEdicion()
    {
        $this->editandoId = null;
        $this->nuevoConteo = 0;
    }

    public function guardarConteo()
    {
        $this->validate([
            'nuevoConteo' => 'required|numeric|min:0'
        ]);

        $registro = InventarioConteo::find($this->editandoId);
        if ($registro) {
            $registro->conteo_fisico = $this->nuevoConteo;
            $registro->save();
            
            $this->dispatch('alerta-exito', mensaje: 'Conteo actualizado correctamente.');
        }
        
        $this->cancelarEdicion();
    }

    public function eliminarRegistro($id)
    {
        $registro = InventarioConteo::find($id);
        if ($registro) {
            $registro->delete();
            $this->dispatch('alerta-exito', mensaje: 'Registro de escaneo eliminado de la base de datos.');
        }
    }
    // --- FIN LÓGICA DE EDICIÓN ---

    public function render()
    {
        // 1. Identifica al usuario y sus locales permitidos
        $userId = Auth::id();
        $localesAsignados = DB::table('user_sucursal')
                              ->where('user_id', $userId)
                              ->pluck('sucursal_id');

        // 2. Filtra el selector de sucursales en la vista
        $sucursales = Inventario::select('codLocal', 'nombre_local')
                                ->whereIn('codLocal', $localesAsignados)
                                ->distinct()
                                ->get();
        
        $inventarios = collect();
        if ($this->sucursalId) {
            // Validación doble de seguridad
            if ($localesAsignados->contains($this->sucursalId)) {
                $inventarios = Inventario::where('codLocal', $this->sucursalId)
                                         ->orderBy('id', 'desc')
                                         ->get();
            }
        }

        $registros = collect();
        
        if ($this->inventarioId) {
            $registros = InventarioConteo::leftJoin('metros', 'inventario_conteo.metro_id', '=', 'metros.id')
                ->select('inventario_conteo.*', 'metros.numeroMetro as nombre_metro')
                ->where('inventario_conteo.inventario_id', $this->inventarioId)
                ->when($this->metro, function($query) {
                    $query->where('metros.numeroMetro', $this->metro);
                })
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        // 3. Retornamos solo las variables necesarias
        return view('livewire.reporte-inventario', compact(
            'sucursales', 
            'inventarios', 
            'registros'
        ));
    }

    public function limpiarFiltros()
    {
        $this->metro = ''; 
    }

    public function exportarExcel()
    {
        if (!$this->inventarioId) {
            return; 
        }
        return Excel::download(new ReporteInventarioExport($this->inventarioId, $this->metro), 'reporte_inventario.xlsx');
    }

    public function exportarPDF()
    {
        // Valida que haya un inventario seleccionado
        if (!$this->inventarioId) {
            $this->dispatch('alerta-exito', mensaje: 'Debe seleccionar un inventario para exportar.');
            return; 
        }

        // 1. Trae los mismos registros filtrados
        $registros = InventarioConteo::leftJoin('metros', 'inventario_conteo.metro_id', '=', 'metros.id')
            ->select('inventario_conteo.*', 'metros.numeroMetro as nombre_metro')
            ->where('inventario_conteo.inventario_id', $this->inventarioId)
            ->when($this->metro, function($query) {
                $query->where('metros.numeroMetro', $this->metro);
            })
            ->get();

        // 2. Trae datos de cabecera
        $inventario = Inventario::find($this->inventarioId);
        
        // 3. Genera el PDF 
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.planilla-codigos', [
            'registros' => $registros,
            'inventario' => $inventario,
            'filtroMetro' => $this->metro
        ])->setPaper('a4', 'landscape');

        // 4. Descarga el archivo
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Codigos_Inventario.pdf');
    }
}