@extends('layouts.app')
@section('title', __('Welcome'))
@section('content')
<div class="container-fluid">
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header"><h5><span class="text-center bi-house"></span> @yield('title')</h5></div>
            <div class="card-body">
                <livewire:reporte-inventario />
            </div>
        </div>
    </div>
</div>
</div>
@endsection