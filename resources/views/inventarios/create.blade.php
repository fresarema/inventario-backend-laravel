@extends('adminlte::page')

@section('title', 'Abrir Inventario')

@section('content_header')
    <h1>Abrir Nuevo Proceso de Inventario</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Detalles del Proceso</h3>
            </div>
            
            <form action="{{ route('inventarios.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    
                    <div class="form-group">
                        <label>Título / Identificador del Inventario</label>
                        <input type="text" name="inventario" class="form-control" placeholder="Ej: Auditoría Sorpresa Agosto" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Local (Sucursal)</label>
                            <select name="codLocal" class="form-control" required>
                                <option value="" disabled selected>Seleccione un local...</option>
                                @foreach($locales as $local)
                                    <option value="{{ $local->codLocal }}">{{ $local->nombre_local }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Fecha de Ejecución</label>
                            <input type="date" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Observaciones (Opcional)</label>
                        <textarea name="observacion" class="form-control" rows="3" placeholder="Instrucciones especiales para los operarios..."></textarea>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Crear y Abrir Inventario</button>
                    <a href="{{ route('inventarios.index') }}" class="btn btn-default float-right">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop