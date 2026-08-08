@extends('adminlte::page')
@section('title', 'Dashboard Administración')

@section('adminlte_css')
    @livewireStyles
    <script src="https://cdn.tailwindcss.com"></script>
@stop

@section('content_header')
    <h1>Principal</h1>
@stop

@section('content')
    @yield('content')
    {{ $slot ?? '' }}
@stop

<!-- Creamos la sección de JS que te faltaba para que los botones y la búsqueda en vivo funcionen -->
@section('adminlte_js')
    @livewireScripts
@stop