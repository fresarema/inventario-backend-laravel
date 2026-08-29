<div>
    <!-- Encabezado y Botón Nuevo -->
    <div class="row mb-3">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Gestión de Inventarios</h1>
        </div>
        <div class="col-sm-6 text-right">
            <button wire:click="abrirModalNuevo" class="btn btn-primary">
                <i class="fas fa-plus"></i> Abrir Nuevo Inventario
            </button>
        </div>
    </div>

    <!-- Modal Formulario -->
    <div wire:ignore.self class="modal fade" id="modalInventario" tabindex="-1" role="dialog" aria-hidden="true">
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
                        
                        <div class="form-group">
                            <label>Título / Identificador del Inventario</label>
                            <input type="text" wire:model.defer="titulo_inventario" class="form-control @error('titulo_inventario') is-invalid @enderror" placeholder="Ej: Auditoría Sorpresa Agosto">
                            @error('titulo_inventario') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Local (Sucursal)</label>
                                <select wire:model.defer="codLocal" class="form-control @error('codLocal') is-invalid @enderror">
                                    <option value="">Seleccione un local...</option>
                                    @foreach($locales as $local)
                                        <option value="{{ $local->codLocal }}">{{ $local->nombre_local }}</option>
                                    @endforeach
                                </select>
                                @error('codLocal') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Fecha de Ejecución</label>
                                <input type="date" wire:model.defer="fecha" class="form-control @error('fecha') is-invalid @enderror">
                                @error('fecha') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Observaciones (Opcional)</label>
                            <textarea wire:model.defer="observacion" class="form-control @error('observacion') is-invalid @enderror" rows="3" placeholder="Instrucciones especiales para los operarios..."></textarea>
                            @error('observacion') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="guardar">Guardar Inventario</span>
                            <span wire:loading wire:target="guardar">Procesando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de Inventarios -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px">ID</th>
                        <th>Título del Proceso</th>
                        <th>Local</th>
                        <th>Fecha Asignada</th>
                        <th>Estado</th>
                        <th style="width: 150px" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventarios as $inv)
                        <tr>
                            <td>{{ $inv->id }}</td>
                            <td><strong>{{ $inv->inventario }}</strong></td>
                            <td>{{ $inv->nombre_local }} (Cód: {{ $inv->codLocal }})</td>
                            <td>{{ \Carbon\Carbon::parse($inv->fecha)->format('d-m-Y') }}</td>
                            <td>
                                @if($inv->estado == 1)
                                    <span class="badge badge-success">Abierto</span>
                                @elseif($inv->estado == 0)
                                    <span class="badge badge-danger">Cerrado</span>
                                @else
                                    <span class="badge badge-secondary">Estado: {{ $inv->estado }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <!-- Botón Ver Detalles (Redirige a la vista completa) -->
                                <a href="{{ route('inventarios.show', $inv->id) }}" class="btn btn-sm btn-info" title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Botón Editar -->
                                <button wire:click="editar({{ $inv->id }})" class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Botón Cambiar Estado (Cerrar / Reabrir) -->
                                @if($inv->estado == 1)
                                    <button onclick="confirmarCambioEstado({{ $inv->id }}, 0, '¿Cerrar este inventario?', 'Los operarios no podrán sincronizar más datos.')" class="btn btn-sm btn-warning" title="Cerrar Inventario">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                @else
                                    <button onclick="confirmarCambioEstado({{ $inv->id }}, 1, '¿Reabrir este inventario?', 'Se volverán a aceptar lecturas desde la app.')" class="btn btn-sm btn-success" title="Reabrir Inventario">
                                        <i class="fas fa-unlock"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">No hay inventarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Escuchar eventos para abrir y cerrar el modal
    window.addEventListener('abrir-modal', event => {
        $('#modalInventario').modal('show');
    });

    window.addEventListener('cerrar-modal', event => {
        $('#modalInventario').modal('hide');
    });

    // Escuchar el mensaje de éxito para lanzar SweetAlert2
    window.addEventListener('alerta-exito', event => {
        Swal.fire({
            icon: 'success',
            title: '¡Listo!',
            text: event.detail.mensaje || event.detail[0].mensaje, 
            timer: 3000,
            showConfirmButton: false
        });
    });

    // Función JavaScript para confirmar con SweetAlert2 antes de cambiar estado
    function confirmarCambioEstado(id, nuevoEstado, titulo, texto) {
        Swal.fire({
            title: titulo,
            text: texto,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: nuevoEstado === 0 ? '#f39c12' : '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, proceder',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Llama directamente al método cambiarEstado del componente Livewire
                @this.call('cambiarEstado', id, nuevoEstado);
            }
        });
    }
</script>