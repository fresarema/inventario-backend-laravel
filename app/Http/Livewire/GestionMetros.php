<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Metro;
use App\Models\MaestroLocal; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GestionMetros extends Component
{
    public $metros;
    public $locales;
    public $nivel = 'Superficie'; 
    
    public $metroDesde;
    public $metroHasta;
    
    public $estado = 1;
    public $local_id = ''; 

    public $selectedMetros = [];

    public function mount()
    {
        $userId = Auth::id();
        $localesAsignados = DB::table('user_sucursal')
                              ->where('user_id', $userId)
                              ->pluck('sucursal_id');


        $this->locales = MaestroLocal::whereIn('codLocal', $localesAsignados)
                           ->orderBy('nombre_local')
                           ->get();
                           
        $this->cargarMetros();
    }

    public function cargarMetros()
    {
        $userId = Auth::id();
        $localesAsignados = DB::table('user_sucursal')
                              ->where('user_id', $userId)
                              ->pluck('sucursal_id');

        $this->metros = Metro::join('maestro_locales', 'metros.local_id', '=', 'maestro_locales.codLocal')
            ->whereIn('metros.local_id', $localesAsignados) 
            ->select('metros.*', 'maestro_locales.nombre_local')
            ->orderBy('maestro_locales.nombre_local', 'asc')
            ->orderByRaw('CAST(metros.numeroMetro AS INT) ASC') 
            ->get();
    }

    public function guardar()
    {
        $this->metroDesde = trim($this->metroDesde);
        if ($this->metroHasta) {
            $this->metroHasta = trim($this->metroHasta);
        }

        $this->validate([
            'local_id' => 'required',
            'metroDesde' => 'required|integer|min:1',
            'metroHasta' => 'nullable|integer|gte:metroDesde',
            'estado' => 'required|boolean',
        ]);

        $hasta = $this->metroHasta ? $this->metroHasta : $this->metroDesde;
        
        $creados = 0;
        $omitidos = 0;

        for ($i = $this->metroDesde; $i <= $hasta; $i++) {
            $numeroLimpio = (string)$i;

            $existe = Metro::where('local_id', $this->local_id)
                           ->where('numeroMetro', $numeroLimpio)
                           ->where('nivel', $this->nivel)
                           ->first();
            
            if(!$existe) {
                Metro::create([
                    'numeroMetro' => $numeroLimpio,
                    'estado' => $this->estado,
                    'nivel' => $this->nivel,
                    'local_id' => $this->local_id,
                ]);
                $creados++;
            } else {
                $omitidos++;
            }
        }

        $this->reset(['metroDesde', 'metroHasta']); 
        $this->cargarMetros();
        
        $mensaje = "Se crearon {$creados} pasillos exitosamente.";
        if ($omitidos > 0) {
            $mensaje .= " Se omitieron {$omitidos} que ya existían en la sucursal.";
        }
        
        $this->dispatchBrowserEvent('alerta-exito', ['mensaje' => $mensaje]);
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
            $this->dispatchBrowserEvent('alerta-exito', ['mensaje' => 'Pasillo eliminado permanentemente.']);
        }
    }

    public function cerrarSeleccionados()
    {
        if (empty($this->selectedMetros)) return;

        Metro::whereIn('id', $this->selectedMetros)->update(['estado' => 0]); 
        $this->selectedMetros = []; 
        $this->cargarMetros(); 
        
        $this->dispatchBrowserEvent('alerta-exito', ['mensaje' => 'Pasillos seleccionados cerrados correctamente.']);
    }

    public function eliminarSeleccionados()
    {
        if (empty($this->selectedMetros)) return;

        Metro::whereIn('id', $this->selectedMetros)->delete();
        $this->selectedMetros = []; 
        $this->cargarMetros(); 
        
        $this->dispatchBrowserEvent('alerta-exito', ['mensaje' => 'Pasillos eliminados de forma permanente.']);
    }

    public function render()
    {
        return view('livewire.gestion-metros');
    }
}