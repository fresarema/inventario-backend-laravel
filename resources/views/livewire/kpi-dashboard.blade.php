<div wire:poll.15s class="row">
    <!-- KPI 1: Inventarios Activos -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $inventariosActivos }}</h3>
                <p>Inventarios Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
    </div>

    <!-- KPI 2: Locales en Proceso -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $localesEnProceso }}</h3>
                <p>Locales en Proceso</p>
            </div>
            <div class="icon">
                <i class="fas fa-store"></i>
            </div>
        </div>
    </div>

    <!-- KPI 3: Última Sincronización -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $ultimaSincronizacion ? \Carbon\Carbon::parse($ultimaSincronizacion)->format('d/m H:i') : 'Sin datos' }}</h3>
                <p>Última Sincronización</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>