@extends('adminlte::page')

@section('title', 'Nuevo Usuario')

@section('content_header')
    <h1>Crear Nuevo Usuario</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Datos del Personal</h3>
            </div>
            
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nombre Completo</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Juan Pérez" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>RUT</label>
                            <input type="text" name="rut_usuario" class="form-control" placeholder="12345678-9" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Correo Electrónico (Login)</label>
                            <input type="email" name="email" class="form-control" placeholder="correo@empresa.cl" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Contraseña Temporal</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tipo de Usuario</label>
                        <select name="tipo" class="form-control" required>
                            <option value="Operario">Operario (App Móvil)</option>
                            <option value="Administrador">Administrador (Panel Web)</option>
                        </select>
                    </div>

                    <hr>
                    
                    <div class="form-group">
                        <label>Asignación de Locales (Sucursales permitidas)</label>
                        <p class="text-muted text-sm">Selecciona los locales donde este usuario podrá realizar inventarios.</p>
                        <div class="row">
                            @foreach($sucursales as $suc)
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="sucursales[]" id="suc_{{ $suc->codLocal }}" value="{{ $suc->codLocal }}">
                                    <label for="suc_{{ $suc->codLocal }}" class="custom-control-label">{{ $suc->nombre_local }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-default float-right">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop