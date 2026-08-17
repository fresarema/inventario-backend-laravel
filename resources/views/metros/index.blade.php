@extends('adminlte::page')

@section('title', 'Gestión de Pasillos')

@section('content_header')
    <h1>Administración de Pasillos y Metros</h1>
@stop

@section('content')
    <div class="container-fluid">
        <livewire:gestion-metros />
    </div>
@stop