@extends('adminlte::page')

@section('title', 'Gestión de Inventarios')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@stop


@section('content')
    <livewire:inventario-component />
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