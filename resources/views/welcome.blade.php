@extends('layouts.app')
@section('title', __('Administracion Unico'))
@section('content_header')
    <h1 class="m-0 text-dark">Resumen Gerencial</h1>
@stop
@section('content')
    <livewire:kpi-dashboard />
@endsection