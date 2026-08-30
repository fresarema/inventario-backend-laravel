<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Título Principal -->
    <div class="flex items-center mb-6">
        <div class="bg-blue-600 p-2 rounded-lg mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Panel de Control Analítico</h1>
    </div>

    <!-- SECCIÓN DE MÉTRICAS (KPIs) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 border-l-4 border-l-blue-500">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Procesos Abiertos</div>
            <div class="text-3xl font-bold text-gray-800">{{ $inventariosActivos }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 border-l-4 border-l-indigo-500">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Sucursales en Auditoría</div>
            <div class="text-3xl font-bold text-gray-800">{{ $localesEnProceso }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 border-l-4 border-l-emerald-500">
            <div class="text-xs font-bold text-gray-500 uppercase mb-1">Última Sincronización</div>
            <div class="text-3xl font-bold text-gray-800">
                @if($ultimaSincronizacion)
                    {{ \Carbon\Carbon::parse($ultimaSincronizacion)->format('H:i') }}
                @else
                    --:--
                @endif
            </div>
        </div>
    </div>

    <!-- Tarjeta de Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="bg-blue-600 text-white px-4 py-3 rounded-t-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span class="font-semibold">Filtros de Búsqueda</span>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <!-- Select Local -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Seleccionar Local</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <select wire:model.live="sucursalId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5">
                            <option value="">Seleccionar...</option>
                            @foreach($sucursales as $suc)
                                <option value="{{ $suc->codLocal }}">{{ $suc->nombre_local }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Select Inventario Dinámico -->
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Seleccionar Inventario</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <select wire:model.live="inventarioId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" @if(empty($sucursalId)) disabled @endif>
                            
                            <option value="">
                                {{ $sucursalId ? 'Seleccione un inventario...' : 'Seleccione un local primero' }}
                            </option>
                            
                            @if(isset($inventarios))
                                @foreach($inventarios as $inv)
                                    <option value="{{ $inv->id }}">
                                        ID: {{ $inv->id }} - {{ $inv->nombre }} {{ $inv->estado == 1 ? '(Abierto)' : '(Cerrado)' }}
                                    </option>
                                @endforeach
                            @endif
                            
                        </select>
                    </div>
                </div>

                <!-- Input Metro -->
                <div class="flex gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Metro / Pasillo</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 11a9 9 0 019 9"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4a9 9 0 019 9"></path></svg>
                            </div>
                            <!-- wire:model.live hace que la tabla se actualice sola mientras escribes -->
                            <input type="text" wire:model.live="metro" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Ej: 1000">
                        </div>
                    </div>
                    <button wire:click="limpiarFiltros" class="mt-6 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg text-sm px-4 py-2.5 hover:bg-gray-50">
                        Todo
                    </button>
                    <button class="mt-6 bg-blue-600 text-white font-medium rounded-lg text-sm px-4 py-2.5 hover:bg-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor de Botones de Exportación -->
    <div class="flex gap-2 mb-4">
        <button wire:click="exportarExcel" class="btn btn-success">
            <i class="fas fa-file-excel"></i> EXPORTAR A EXCEL
        </button>
        

        <button wire:click="exportarPDF" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> EXPORTAR PDF CODIGOS
            <span wire:loading wire:target="exportarPDF" class="spinner-border spinner-border-sm ml-2" role="status" aria-hidden="true"></span>
        </button>
    </div>

    <!-- Tabla de Resultados -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th scope="col" class="px-6 py-4 font-bold">ID</th>
                    <th scope="col" class="px-6 py-4 font-bold">METRO</th>
                    <th scope="col" class="px-6 py-4 font-bold">CÓDIGO PRODUCTO</th>
                    <th scope="col" class="px-6 py-4 font-bold">DESCRIPCIÓN</th>
                    <th scope="col" class="px-6 py-4 font-bold">STOCK SISTEMA</th>
                    <th scope="col" class="px-6 py-4 font-bold">CONTEO FÍSICO</th>
                    <th scope="col" class="px-6 py-4 font-bold text-center">DIFERENCIA</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $registro->id }}</td>
                        <td>
                            <input type="checkbox" class="mr-2"> 
                            <span class="font-weight-bold text-dark">
                                {{ $registro->nombre_metro ?? $registro->metro_id ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $registro->codigo_producto ?? $registro->producto_codigo }}</td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $registro->descripcion_producto ?? ($registro->producto ? $registro->descripcion_producto : 'Producto sin descripción') }}
                        </td>
                        <td class="px-6 py-4 font-medium">{{ $registro->stock_sistema ?? 0 }}</td>
                        <td class="px-6 py-4">
                            @if($editandoId === $registro->id)
                                <!-- MODO EDICIÓN: Input y botones de guardar/cancelar -->
                                <div class="flex items-center gap-2">
                                    <input type="number" step="0.01" wire:model="nuevoConteo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-24 p-1">
                                    
                                    <button wire:click="guardarConteo" class="text-emerald-600 hover:text-emerald-800 bg-emerald-100 p-1 rounded" title="Guardar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                    
                                    <button wire:click="cancelarEdicion" class="text-gray-500 hover:text-gray-700 bg-gray-200 p-1 rounded" title="Cancelar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @else
                                <!-- MODO LECTURA: Número y botones de lápiz/basurero -->
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-blue-600">{{ $registro->conteo_fisico ?? 0 }}</span>
                                    
                                    <!-- Botón Editar -->
                                    <button wire:click="activarEdicion({{ $registro->id }}, {{ $registro->conteo_fisico ?? 0 }})" class="text-blue-400 hover:text-blue-600 transition-colors" title="Editar Conteo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    
                                    <!-- Botón Eliminar (Dispara SweetAlert2) -->
                                    <button onclick="confirmarEliminacionRegistro({{ $registro->id }})" class="text-red-400 hover:text-red-600 transition-colors" title="Eliminar Registro">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $stock = $registro->stock_sistema ?? 0;
                                $conteo = $registro->conteo_fisico ?? 0;
                                $diferencia = $conteo - $stock;
                            @endphp
                            
                            @if($diferencia > 0)
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-0.5 rounded border border-emerald-200">+{{ $diferencia }}</span>
                            @elseif($diferencia < 0)
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded border border-red-200">{{ $diferencia }}</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-bold px-2.5 py-0.5 rounded border border-gray-200">0</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No hay registros para los filtros seleccionados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 text-sm text-gray-600 bg-white border-t">
            Mostrando {{ $registros->count() }} registros
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                    // Llama al método eliminarRegistro en Livewire
                    @this.call('eliminarRegistro', id);
                }
            });
        }
    </script>
</div>