<div>
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h3 class="card-title font-weight-bold mb-0" style="color: #1e293b;">
                <i class="fas fa-file-archive text-secondary mr-2"></i> Historial de Inventarios Consolidados
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead style="background-color: #f8fafc; color: #475569;">
                    <tr>
                        <th>ID</th>
                        <th>Título del Proceso</th>
                        <th>Local</th>
                        <th>Fecha Asignada</th>
                        <th>Estado</th>
                        <th>Acciones (Exportar)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventarios as $inv)
                        <tr>
                            <td class="align-middle">{{ $inv->id }}</td>
                            <td class="align-middle font-weight-bold">{{ $inv->inventario }}</td>
                            <td class="align-middle">{{ $inv->nombre_local }} (Cód: {{ $inv->codLocal }})</td>
                            {{-- Formateamos la fecha para que se vea ordenada --}}
                            <td class="align-middle">{{ \Carbon\Carbon::parse($inv->fecha)->format('d-m-Y') }}</td>
                            <td class="align-middle">
                                <span class="badge" style="background-color: #64748b; color: white; padding: 5px 10px;">
                                    Cerrado
                                </span>
                            </td>
                            <td class="align-middle">
                                <button class="btn btn-sm text-white mr-1" style="background-color: #ef4444;" title="Descargar PDF">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button class="btn btn-sm text-white" style="background-color: #10b981;" title="Descargar Excel">
                                    <i class="fas fa-file-excel"></i> Excel
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-3 text-light"></i><br>
                                Aún no hay registros de inventarios cerrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inventarios->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $inventarios->links() }}
            </div>
        @endif
    </div>
</div>