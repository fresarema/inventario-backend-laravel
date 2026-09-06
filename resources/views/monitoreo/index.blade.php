@extends('adminlte::page')

@section('title', 'Monitor en Tiempo Real')

@section('content_header')
    <h1>Monitor de Sincronizaciones en Vivo</h1>
@stop

@section('content')
    
    <div class="container-fluid">
        <livewire:reporte-inventario />
    </div>
@stop