<div>
    <!-- Encabezado y Botón Nuevo -->
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Gestión de Usuarios</h1>
        </div>
        <div class="col-sm-6 text-right">
            <button wire:click="abrirModalNuevo" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </button>
        </div>
    </div>

    <!-- Modal Formulario -->
    <div wire:ignore.self class="modal fade" id="modalUsuario" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ $modalTitle }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="guardar">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Nombre Completo</label>
                                <input type="text" wire:model.defer="name" class="form-control @error('name') is-invalid @enderror" placeholder="Ej: Juan Pérez">
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>RUT</label>
                                <input type="text" wire:model.defer="rut_usuario" class="form-control @error('rut_usuario') is-invalid @enderror" placeholder="12345678-9">
                                @error('rut_usuario') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Correo Electrónico (Login)</label>
                                <input type="email" wire:model.defer="email" class="form-control @error('email') is-invalid @enderror" placeholder="correo@empresa.cl">
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Contraseña {{ $usuario_id ? '(Opcional al editar)' : '' }}</label>
                                <input type="password" wire:model.defer="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tipo de Usuario</label>
                            <select wire:model.defer="tipo" class="form-control @error('tipo') is-invalid @enderror">
                                <option value="Operario">Operario (App Móvil)</option>
                                <option value="Administrador">Administrador (Panel Web)</option>
                            </select>
                            @error('tipo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <hr>
                        
                        <div class="form-group">
                            <label>Asignación de Locales (Sucursales permitidas)</label>
                            <p class="text-muted text-sm">Selecciona los locales donde este usuario podrá realizar inventarios.</p>
                            <div class="row">
                                @foreach($sucursales as $suc)
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" wire:model.defer="sucursalesSeleccionadas" id="suc_{{ $suc->codLocal }}" value="{{ $suc->codLocal }}">
                                        <label for="suc_{{ $suc->codLocal }}" class="custom-control-label">{{ $suc->nombre_local }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="guardar">Guardar</span>
                            <span wire:loading wire:target="guardar">Procesando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">ID</th>
                        <th>RUT</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Tipo</th>
                        <th>Locales Asignados</th>
                        <th style="width: 120px" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
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
                                @forelse($usuario->sucursales as $sucursal)
                                    <span class="badge badge-info">{{ $sucursal->nombre_local }}</span>
                                @empty
                                    <span class="text-muted text-sm">Sin locales</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <button wire:click="editar({{ $usuario->id }})" class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="eliminar({{ $usuario->id }})" onclick="return confirm('¿Estás seguro de eliminar este usuario?') || event.stopImmediatePropagation()" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No hay usuarios registrados en el sistema.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>