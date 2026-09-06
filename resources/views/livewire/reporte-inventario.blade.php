<div>
    <!-- Tarjeta de Filtros -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter"></i> Filtros de Búsqueda</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Select Local -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Seleccionar Local</label>
                        <select wire:model.live="sucursalId" class="form-control">
                            <option value="">Seleccionar...</option>
                            @foreach($sucursales as $suc)
                                <option value="{{ $suc->codLocal }}">{{ $suc->nombre_local }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Select Inventario Dinámico -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Seleccionar Inventario</label>
                        <select wire:model.live="inventarioId" class="form-control" @if(empty($sucursalId)) disabled @endif>
                            <option value="">{{ $sucursalId ? 'Seleccione un inventario...' : 'Seleccione un local primero' }}</option>
                            @if(isset($inventarios))
                                @foreach($inventarios as $inv)
                                    <option value="{{ $inv->id }}">ID: {{ $inv->id }} - {{ $inv->nombre }} {{ $inv->estado == 1 ? '(Abierto)' : '(Cerrado)' }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Input Metro -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Metro / Pasillo</label>
                        <div class="input-group">
                            <input type="text" wire:model.live="metro" class="form-control" placeholder="Ej: 1000">
                            <span class="input-group-append">
                                <button wire:click="limpiarFiltros" type="button" class="btn btn-outline-secondary">Todo</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Exportación -->
    <div class="mb-3">
        <button wire:click="exportarExcel" class="btn btn-success">
            <i class="fas fa-file-excel"></i> EXPORTAR A EXCEL
        </button>
        <button wire:click="exportarPDF" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> EXPORTAR PDF CÓDIGOS
            <span wire:loading wire:target="exportarPDF" class="spinner-border spinner-border-sm ml-2" role="status" aria-hidden="true"></span>
        </button>
    </div>

    <!-- Tabla de Resultados -->
    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>METRO</th>
                        <th>CÓDIGO PRODUCTO</th>
                        <th>DESCRIPCIÓN</th>
                        <th>STOCK SISTEMA</th>
                        <th>CONTEO FÍSICO</th>
                        <th class="text-center">DIFERENCIA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $registro)
                        <tr>
                            <td>{{ $registro->id }}</td>
                            <td><span class="font-weight-bold">{{ $registro->nombre_metro ?? $registro->metro_id ?? 'N/A' }}</span></td>
                            <td>{{ $registro->codigo_producto ?? $registro->producto_codigo }}</td>
                            <td>{{ Str::limit($registro->descripcion_producto ?? ($registro->producto ? $registro->descripcion_producto : 'Producto sin descripción'), 40) }}</td>
                            <td>{{ $registro->stock_sistema ?? 0 }}</td>
                            <td>
                                @if($editandoId === $registro->id)
                                    <!-- MODO EDICIÓN -->
                                    <div class="d-flex align-items-center">
                                        <input type="number" step="0.01" wire:model="nuevoConteo" class="form-control form-control-sm mr-2" style="width: 80px;">
                                        <button wire:click="guardarConteo" class="btn btn-success btn-sm mr-1" title="Guardar"><i class="fas fa-check"></i></button>
                                        <button wire:click="cancelarEdicion" class="btn btn-secondary btn-sm" title="Cancelar"><i class="fas fa-times"></i></button>
                                    </div>
                                @else
                                    <!-- MODO LECTURA -->
                                    <div class="d-flex align-items-center">
                                        <span class="font-weight-bold text-primary mr-3">{{ $registro->conteo_fisico ?? 0 }}</span>
                                        <button wire:click="activarEdicion({{ $registro->id }}, {{ $registro->conteo_fisico ?? 0 }})" class="btn btn-outline-primary btn-xs mr-1" title="Editar Conteo"><i class="fas fa-pen"></i></button>
                                        <button onclick="confirmarEliminacionRegistro({{ $registro->id }})" class="btn btn-outline-danger btn-xs" title="Eliminar Registro"><i class="fas fa-trash"></i></button>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $stock = $registro->stock_sistema ?? 0;
                                    $conteo = $registro->conteo_fisico ?? 0;
                                    $diferencia = $conteo - $stock;
                                @endphp
                                
                                @if($diferencia > 0)
                                    <span class="badge badge-success">+{{ $diferencia }}</span>
                                @elseif($diferencia < 0)
                                    <span class="badge badge-danger">{{ $diferencia }}</span>
                                @else
                                    <span class="badge badge-secondary">0</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay registros para los filtros seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted text-sm">
            Mostrando {{ $registros->count() }} registros
        </div>
    </div>
    
    <script>
        // No es necesario importar SweetAlert2 de nuevo si ya está en tu layout principal (AdminLTE)
        window.addEventListener('alerta-exito', event => {
            Swal.fire({
                icon: 'success',
                title: '¡Operación Exitosa!',
                text: event.detail.mensaje || event.detail[0].mensaje, 
                timer: 2000,
                showConfirmButton: false
            });
        });

        function confirmarEliminacionRegistro(id) {
            Swal.fire({
                title: '¿Eliminar este registro?',
                text: "El conteo físico se borrará permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('eliminarRegistro', id);
                }
            });
        }
    </script>
</div>