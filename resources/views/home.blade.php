@extends('adminlte::page')

@section('title', 'Dashboard Principal')

@section('content_header')
    <h1>Panel de Control Analítico</h1>
@stop

@section('content')
    <!-- SECCIÓN 1: Tarjetas de Métricas (KPIs) -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $inventariosActivos }}</h3>
                    <p>Procesos Abiertos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $localesEnProceso }}</h3>
                    <p>Sucursales en Auditoría</p>
                </div>
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>
                        @if($ultimaSincronizacion)
                            {{ \Carbon\Carbon::parse($ultimaSincronizacion)->format('H:i') }}
                        @else
                            --:--
                        @endif
                    </h3>
                    <p>Última Sincronización</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: Motor de Reportes y Filtros -->
    <div class="card card-primary card-outline mt-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <label>Seleccionar Local</label>
                    <select id="filtro-local" class="form-control">
                        <option value="">Seleccionar...</option>
                        @foreach($locales as $local)
                            <option value="{{ $local->codLocal }}">{{ $local->nombre_local }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Seleccionar Inventario</label>
                    <select id="filtro-inventario" class="form-control" disabled>
                        <option value="">Seleccione un local primero</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Metro / Pasillo</label>
                    <input type="text" id="filtro-metro" class="form-control" placeholder="Ej: 1">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="btn-buscar" class="btn btn-primary w-100"><i class="fas fa-search"></i> Buscar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: Tabla de Resultados -->
    <div class="card">
        <div class="card-header border-0">
            <button class="btn btn-success"><i class="fas fa-file-excel"></i> Exportar a Excel</button>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle text-center" id="tabla-reporte">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Metro</th>
                        <th>Código Producto</th>
                        <th>Descripción</th>
                        <th>Stock Sistema</th>
                        <th>Conteo Físico</th>
                        <th>Diferencia</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-tabla">
                    <tr>
                        <td colspan="7" class="text-center py-4">Utilice los filtros para cargar los registros.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        // Aquí implementaremos la lógica AJAX para que los filtros funcionen en cascada
    </script>
@stop