@extends('adminlte::page')

@section('title', 'Gestión de Inventarios')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Gestión de Inventarios</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('inventarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Abrir Nuevo Inventario
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <table id="tabla-inventarios" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">ID</th>
                        <th>Título del Proceso</th>
                        <th>Local</th>
                        <th>Fecha Asignada</th>
                        <th>Estado</th>
                        <th style="width: 100px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventarios as $inv)
                        <tr>
                            <td>{{ $inv->id }}</td>
                            <td><strong>{{ $inv->inventario }}</strong></td>
                            <td>{{ $inv->nombre_local }} (Cód: {{ $inv->codLocal }})</td>
                            <td>{{ \Carbon\Carbon::parse($inv->fecha)->format('d-m-Y') }}</td>
                            <td>
                                @if($inv->estado == 1 || $inv->estado == '1')
                                    <span class="badge badge-success">Abierto</span>
                                @elseif($inv->estado == 2 || $inv->estado == '2')
                                    <span class="badge badge-warning">En Progreso</span>
                                @elseif($inv->estado == 0 || $inv->estado == '0')
                                    <span class="badge badge-danger">Cerrado</span>
                                @else
                                    <span class="badge badge-secondary">Estado: {{ $inv->estado }}</span>
                                @endif
                            </td>
                            <td>
                                <!-- Botón de Detalles (Lo programaremos después) -->
                                <a href="{{ route('inventarios.show', $inv->id) }}" class="btn btn-sm btn-info" title="Ver Detalles"><i class="fas fa-eye"></i></a>
                                
                                <!-- Botón de Cerrar (Solo si está Abierto o En Progreso) -->
                                @if($inv->estado == 1 || $inv->estado == '1' || $inv->estado == 2 || $inv->estado == '2')
                                    <form action="{{ route('inventarios.cerrar', $inv->id) }}" method="POST" class="d-inline form-cerrar">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-warning" title="Cerrar Inventario">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#tabla-inventarios').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "ordering": false
            });

            // Interceptor para el botón de cerrar inventario
            $('.form-cerrar').submit(function(e){
                e.preventDefault();
                Swal.fire({
                    title: '¿Cerrar este inventario?',
                    text: "Una vez cerrado, los operarios no podrán descargar ni sincronizar más datos a este proceso.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f39c12', // Color amarillo/naranja
                    cancelButtonColor: '#6c757d',  // Color gris
                    confirmButtonText: 'Sí, cerrar proceso',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            });

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@stop