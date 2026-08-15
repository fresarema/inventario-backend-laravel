@extends('adminlte::page')

@section('title', 'Detalle de Inventario')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Detalle del Inventario #{{ $inventario->id }}</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Listado
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Tarjeta de Resumen (Cabecera) -->
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">Información del Proceso</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Título:</strong> <br> {{ $inventario->inventario }}
                </div>
                <div class="col-md-3">
                    <strong>Local (Sucursal):</strong> <br> {{ $inventario->nombre_local }} (Cód: {{ $inventario->codLocal }})
                </div>
                <div class="col-md-3">
                    <strong>Fecha Asignada:</strong> <br> {{ \Carbon\Carbon::parse($inventario->fecha)->format('d-m-Y') }}
                </div>
                <div class="col-md-3">
                    <strong>Estado:</strong> <br> 
                    @if($inventario->estado == 1 || $inventario->estado == '1')
                        <span class="badge badge-success">Abierto</span>
                    @elseif($inventario->estado == 2 || $inventario->estado == '2')
                        <span class="badge badge-warning">En Progreso</span>
                    @elseif($inventario->estado == 0 || $inventario->estado == '0')
                        <span class="badge badge-danger">Cerrado</span>
                    @else
                        <span class="badge badge-secondary">{{ $inventario->estado }}</span>
                    @endif
                </div>
            </div>
            @if($inventario->observacion)
            <div class="row mt-3">
                <div class="col-md-12">
                    <strong>Observaciones:</strong> <br> {{ $inventario->observacion }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Tarjeta de Productos Escaneados (Conteo) -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Registro de Conteo</h3>
        </div>
        <div class="card-body table-responsive">
            <table id="tabla-conteos" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID Conteo</th>
                        <th>Código de Barras</th>
                        <th>Descripción / Producto</th>
                        <th>Cantidad Escaneada</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conteos as $conteo)
                        <tr>
                            <!-- Ajusta estos nombres según las columnas reales de tu tabla 'inventario_conteo' -->
                            <td>{{ $conteo->id }}</td>
                            <td>{{ $conteo->codigo_producto ?? 'N/A' }}</td>
                            <td>{{ $conteo->descripcion_producto ?? 'N/A' }}</td>
                            <td><strong>{{ $conteo->conteo_fisico ?? 0 }}</strong></td>
                            <td>{{ isset($conteo->created_at) ? \Carbon\Carbon::parse($conteo->created_at)->format('d-m-Y H:i') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tabla-conteos').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "order": [[ 0, "desc" ]] // Ordena por el ID más reciente primero
            });
        });
    </script>
@stop