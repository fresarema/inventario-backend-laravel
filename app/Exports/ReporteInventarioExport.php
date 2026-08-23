<?php

namespace App\Exports;

use App\Models\InventarioConteo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReporteInventarioExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $inventarioId;
    protected $metro;

    // Recibe los filtros desde el componente Livewire
    public function __construct($inventarioId, $metro = null)
    {
        $this->inventarioId = $inventarioId;
        $this->metro = $metro;
    }

    // 1. Extrae los datos cruzando con la tabla metros
    public function collection()
    {
        return InventarioConteo::leftJoin('metros', 'inventario_conteo.metro_id', '=', 'metros.id')
            ->select('inventario_conteo.*', 'metros.numeroMetro as nombre_metro')
            ->where('inventario_conteo.inventario_id', $this->inventarioId)
            ->when($this->metro, function($query) {
                $query->where('metros.numeroMetro', $this->metro);
            })
            ->get();
    }

    // 2. Define las cabeceras de las columnas en el Excel
    public function headings(): array
    {
        return [
            'ID Registro',
            'Pasillo / Metro',
            'Código Producto',
            'Descripción',
            'Stock Sistema',
            'Conteo Físico',
            'Diferencia',
            'Fecha de Escaneo',
        ];
    }

    // 3. Mapea los datos de la base de datos a las columnas del Excel
    public function map($registro): array
    {
        // Calcula la diferencia matemática aquí mismo
        $diferencia = $registro->conteo_fisico - $registro->stock_sistema;

        return [
            $registro->id,
            $registro->nombre_metro ?? $registro->metro_id ?? 'N/A',
            '="' . $registro->codigo_producto . '"',
            $registro->descripcion_producto,
            $registro->stock_sistema,
            $registro->conteo_fisico,
            $diferencia,
            $registro->created_at ? $registro->created_at->format('Y-m-d H:i') : 'Sin fecha',
        ];
    }
}