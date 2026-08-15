@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Gestión de Usuarios</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            
            <table id="tabla-usuarios" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">ID</th>
                        <th>RUT</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Tipo</th>
                        <th>Locales Asignados</th>
                        <th style="width: 100px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>{{ $usuario->rut_usuario }}</td>
                            <td>{{ $usuario->name }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>
                                <span class="badge {{ $usuario->tipo == 'Administrador' ? 'badge-danger' : 'badge-success' }}">
                                    {{ $usuario->tipo }}
                                </span>
                            </td>
                            <td>
                                {{-- Recorremos las sucursales asignadas y las mostramos como etiquetas --}}
                                @forelse($usuario->sucursales as $sucursal)
                                    <span class="badge badge-info">{{ $sucursal->nombre_local }}</span>
                                @empty
                                    <span class="text-muted text-sm">Sin locales</span>
                                @endforelse
                            </td>
                            <td>
                                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-info" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop


@section('js')
    <!-- jQuery DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // 1. Inicializar DataTables con traducción al español
            $('#tabla-usuarios').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "ordering":false
            });

            // 2. Escuchar el mensaje flash del controlador y lanzar SweetAlert
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

        $('.form-eliminar').submit(function(e){
                e.preventDefault();
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Este usuario será eliminado del sistema.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            });
    </script>
@stop