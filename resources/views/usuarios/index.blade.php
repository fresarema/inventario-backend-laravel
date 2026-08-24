@extends('adminlte::page')

@section('title', 'Gestión de Usuarios')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop



@section('content')
    <livewire:usuarios-component />

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

    <script>
        // Escucha cuando Livewire pide abrir el modal
        window.addEventListener('abrir-modal', event => {
            $('#modalUsuario').modal('show');
        });

        // Escucha cuando Livewire pide cerrar el modal
        window.addEventListener('cerrar-modal', event => {
            $('#modalUsuario').modal('hide');
        });

        // Escucha el mensaje de éxito para lanzar la alerta
        window.addEventListener('alerta-exito', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Listo!',
                // En Livewire 2 se usa event.detail.mensaje, en Livewire 3 es event.detail[0].mensaje
                text: event.detail.mensaje || event.detail[0].mensaje, 
                timer: 3000,
                showConfirmButton: false
            });
        });
    </script>
@stop