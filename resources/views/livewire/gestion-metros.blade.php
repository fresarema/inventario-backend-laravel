<div>
    

    <div class="row">
        <!-- Formulario de Creación (Columna Izquierda) -->
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-plus-circle mr-1"></i> Abrir Nuevo Pasillo</h3>
                </div>
                
                <form wire:submit.prevent="guardar">
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label for="local_id">Sucursal / Local</label>
                            <select id="local_id" wire:model.defer="local_id" class="form-control @error('local_id') is-invalid @enderror">
                                <option value="">Seleccione un local...</option>
                                @foreach($locales as $local)
                                    <option value="{{ $local->codLocal }}">{{ $local->nombre_local }}</option>
                                @endforeach
                            </select>
                            @error('local_id') 
                                <span class="invalid-feedback">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="metroDesde">Desde el Metro</label>
                                <input type="number" 
                                       id="metroDesde" 
                                       wire:model.defer="metroDesde" 
                                       class="form-control @error('metroDesde') is-invalid @enderror"
                                       placeholder="Ej: 1">
                                @error('metroDesde') 
                                    <span class="invalid-feedback">{{ $message }}</span> 
                                @enderror
                            </div>
                            
                            <div class="col-md-6 form-group">
                                <label for="metroHasta">Hasta (Opcional)</label>
                                <input type="number" 
                                       id="metroHasta" 
                                       wire:model.defer="metroHasta" 
                                       class="form-control @error('metroHasta') is-invalid @enderror"
                                       placeholder="Ej: 50">
                                @error('metroHasta') 
                                    <span class="invalid-feedback">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado Inicial</label>
                            <select id="estado" wire:model.defer="estado" class="form-control @error('estado') is-invalid @enderror">
                                <option value="1">Abierto (Acepta conteos)</option>
                                <option value="0">Cerrado (Bloqueado)</option>
                            </select>
                            @error('estado') 
                                <span class="invalid-feedback">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save mr-1"></i> Crear y Habilitar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Registros (Columna Derecha) -->
        <div class="col-md-8">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-1"></i> Administración de Pasillos</h3>
                </div>
                
                <div class="card-body table-responsive p-0" style="max-height: 500px;">
                    <table class="table table-hover table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th style="width: 10px">ID</th>
                                <th>Sucursal</th>
                                <th>Metro</th>
                                <th class="text-center">Estado Actual</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($metros as $metro)
                                <tr>
                                    <td>{{ $metro->id }}</td>
                                    <td>{{ $metro->nombre_local }}</td>
                                    <td><span class="font-weight-bold text-dark">{{ $metro->numeroMetro }}</span></td>
                                    <td class="text-center">
                                        @if ($metro->estado == 1)
                                            <span class="badge badge-success px-2 py-1">Abierto</span>
                                        @else
                                            <span class="badge badge-danger px-2 py-1">Cerrado</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="cambiarEstado({{ $metro->id }})" 
                                                class="btn btn-sm {{ $metro->estado == 1 ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            <i class="fas {{ $metro->estado == 1 ? 'fa-lock' : 'fa-lock-open' }} mr-1"></i>
                                            {{ $metro->estado == 1 ? 'Cerrar Pasillo' : 'Reabrir Pasillo' }}
                                        </button>

                                        <button onclick="confirmarEliminacion({{ $metro->id }})" class="btn btn-sm btn-outline-danger ml-1" title="Eliminar Pasillo">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted font-italic">
                                        No hay pasillos registrados en el sistema. Utiliza el formulario para comenzar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('alerta-exito', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Operación Finalizada!',
                text: event.detail.mensaje || event.detail[0].mensaje, 
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Entendido'
            });
        });

        // Función para confirmar la eliminación
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Eliminar este pasillo?',
                text: "Esta acción borrará el registro de SQL Server de forma permanente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('eliminar', id);
                }
            });
        }
    </script>
</div>