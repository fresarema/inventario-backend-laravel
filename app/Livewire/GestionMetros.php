<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Metro;
use Illuminate\Support\Facades\DB;

class GestionMetros extends Component
{
    public $metros;
    public $locales; 
    
    // Cambia numeroMetro por un rango
    public $metroDesde;
    public $metroHasta;
    
    public $estado = 1;
    public $local_id = ''; 

    public function mount()
    {
        $this->locales = DB::table('maestro_locales')->orderBy('nombre_local')->get();
        $this->cargarMetros();
    }

    public function cargarMetros()
    {
        $this->metros = Metro::join('maestro_locales', 'metros.local_id', '=', 'maestro_locales.codLocal')
            ->select('metros.*', 'maestro_locales.nombre_local')
            ->orderBy('maestro_locales.nombre_local', 'asc')
            ->orderByRaw('CAST(metros.numeroMetro AS INT) ASC') // Ordena numéricamente
            ->get();
    }

    public function guardar()
    {
        $this->validate([
            'local_id' => 'required',
            'metroDesde' => 'required|integer|min:1',
            // El hasta es opcional, pero si se llena, debe ser mayor o igual al desde
            'metroHasta' => 'nullable|integer|gte:metroDesde',
            'estado' => 'required|boolean',
        ]);

        // Si el usuario no llenó "Hasta", asumimos que solo quiere crear el "Desde"
        $hasta = $this->metroHasta ? $this->metroHasta : $this->metroDesde;
        
        $creados = 0;
        $omitidos = 0;

        for ($i = $this->metroDesde; $i <= $hasta; $i++) {
            $existe = Metro::where('local_id', $this->local_id)
                           ->where('numeroMetro', (string)$i)
                           ->first();
            
            if(!$existe) {
                Metro::create([
                    'numeroMetro' => (string)$i,
                    'estado' => $this->estado,
                    'local_id' => $this->local_id,
                ]);
                $creados++;
            } else {
                $omitidos++;
            }
        }

        $this->reset(['metroDesde', 'metroHasta']); 
        $this->cargarMetros();
        
        // Prepara un mensaje detallado para el usuario
        $mensaje = "Se crearon {$creados} pasillos exitosamente.";
        if ($omitidos > 0) {
            $mensaje .= " Se omitieron {$omitidos} que ya existían en la sucursal.";
        }
        
        // Dispara el evento al navegador
        $this->dispatch('alerta-exito', mensaje: $mensaje);
    }

    public function cambiarEstado($id)
    {
        $metro = Metro::find($id);
        if ($metro) {
            $metro->estado = $metro->estado == 1 ? 0 : 1; 
            $metro->save();
            $this->cargarMetros();
        }
    }

    public function eliminar($id)
    {
        $metro = Metro::find($id);
        if ($metro) {
            $metro->delete(); 
            $this->cargarMetros();
            $this->dispatch('alerta-exito', mensaje: 'Pasillo eliminado permanentemente.');
        }
    }

    public function render()
    {
        return view('livewire.gestion-metros');
    }
}