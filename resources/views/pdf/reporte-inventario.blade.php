<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario #{{ $inventario->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e293b; padding-bottom: 10px; }
        .kpi-container { margin-bottom: 20px; }
        .kpi-box { display: inline-block; width: 30%; background: #f8fafc; padding: 10px; border: 1px solid #cbd5e1; text-align: center; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px; text-align: left; }
        th { background-color: #f1f5f9; color: #1e293b; }
        .text-right { text-align: right; }
        .text-danger { color: #ef4444; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REPORTE FORMAL DE INVENTARIO</h2>
        <p>
            <strong>Local:</strong> {{ $inventario->nombre_local }} (Cód: {{ $inventario->codLocal }}) | 
            <strong>Proceso:</strong> {{ $inventario->inventario }} | 
            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($inventario->fecha)->format('d-m-Y') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Pasillo</th>
                <th>Cód. Producto</th>
                <th>Descripción</th>
                <th class="text-right">Stock Sist.</th>
                <th class="text-right">Conteo Fís.</th>
                <th class="text-right">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registros as $row)
                @php $diferencia = $row->conteo_fisico - $row->stock_sistema; @endphp
                <tr>
                    <td>{{ $row->nombre_metro ?? $row->metro_id ?? 'N/A' }}</td>
                    <td>{{ $row->codigo_producto }}</td>
                    <td>{{ $row->descripcion_producto }}</td>
                    <td class="text-right">{{ number_format($row->stock_sistema, 2) }}</td>
                    <td class="text-right">{{ number_format($row->conteo_fisico, 2) }}</td>
                    <td class="text-right {{ $diferencia != 0 ? 'text-danger' : '' }}">
                        {{ number_format($diferencia, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>