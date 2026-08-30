<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla SATO</title>
    <style>
        /* CSS Nativo estricto para DOMPDF */
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: -20px; 
        }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #333;
            text-transform: uppercase;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 8px 15px;
            margin-bottom: 20px;
            width: 100%;
        }
        .info-box table {
            width: 100%;
            font-size: 11px;
            font-weight: bold;
        }
        /* Cuadrícula para las etiquetas */
        .grid-container {
            width: 100%;
        }
        .etiqueta {
            width: 23%; /* 4 columnas */
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 8px;
            margin: 0.5%;
            box-sizing: border-box;
            vertical-align: top;
            height: 120px;
        }
        .etiqueta-desc {
            font-size: 9px;
            font-weight: bold;
            height: 25px;
            overflow: hidden;
            text-transform: uppercase;
        }
        .etiqueta-codigo {
            font-size: 8px;
            color: #555;
            margin-bottom: 5px;
        }
        .barcode-img {
            text-align: center;
            margin-top: 5px;
        }
        .barcode-img img {
            width: 90%;
            height: 40px;
        }
        .etiqueta-cant {
            text-align: right;
            color: #007bff;
            font-weight: bold;
            font-size: 10px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Planilla Para SATO de Inventario General</h2>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>LOCAL: {{ $inventario->nombre_local ?? 'Todos' }}</td>
                <td>METRO: <span style="background-color: #ffe066; padding: 2px 5px; border-radius: 3px;">{{ $filtroMetro ?: 'Todos' }}</span></td>
                <td>EMISIÓN: {{ date('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="grid-container">
        @php
            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        @endphp

        @foreach($registros as $registro)
            @php
                // Se extrae el código del producto para generar el código de barras
                $codigoProducto = $registro->codigo_producto ?? $registro->producto_codigo;
                // Genera el string en Base64 usando formato CODE_128
                $barcodeBase64 = base64_encode($generator->getBarcode($codigoProducto, $generator::TYPE_CODE_128));
            @endphp
            
            <div class="etiqueta">
                <div class="etiqueta-desc">
                    {{ Str::limit($registro->descripcion_producto ?? 'Producto sin descripción', 55) }}
                </div>
                <div class="etiqueta-codigo">
                    Código: {{ $codigoProducto }}
                </div>
                <div class="barcode-img">
                    <img src="data:image/png;base64,{{ $barcodeBase64 }}" alt="barcode">
                </div>
                <div class="etiqueta-cant">
                    Cantidad: {{ $registro->conteo_fisico ?? 0 }}
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>