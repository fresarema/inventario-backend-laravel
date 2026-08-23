<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inventario;
use App\Exports\ReporteInventarioExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InventarioConteo;

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

    public function exportarExcel($inventarioId)
    {
        // Descarga el reporte dinámicamente usando el ID de la fila
        return Excel::download(
            new ReporteInventarioExport($inventarioId), 
            'consolidado_inventario_' . $inventarioId . '.xlsx'
        );
    }

    public function exportarPDF($inventarioId)
    {
        // 1. Busca la cabecera del inventario
        $inventario = Inventario::findOrFail($inventarioId);

        // 2. Trae todos los registros cruzando con la tabla de metros
        $registros = InventarioConteo::leftJoin('metros', 'inventario_conteo.metro_id', '=', 'metros.id')
            ->select('inventario_conteo.*', 'metros.numeroMetro as nombre_metro')
            ->where('inventario_conteo.inventario_id', $inventarioId)
            ->get();

        // 3. Carga la vista HTML que se creó y le pasa las variables
        $pdf = Pdf::loadView('pdf.reporte-inventario', [
            'inventario' => $inventario,
            'registros' => $registros
        ]);

        // 4. Descarga el archivo
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Reporte_Inventario_' . $inventario->codLocal . '.pdf');
    }
}