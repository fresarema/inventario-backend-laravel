<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Metro;
use Illuminate\Support\Facades\DB;

class GestionMetros extends Component
{
    public $metros;
    public $locales; 
    public $numeroMetro;
    public $estado = 1;
    public $local_id = ''; // Seleccion vacia por defecto

    public function mount()
    {
        // Cargar los locales directamente desde tu tabla maestro_locales
        $this->locales = DB::table('maestro_locales')->orderBy('nombre_local')->get();
        $this->cargarMetros();
    }

    public function cargarMetros()
    {
        // Trae los metros cruzando la info con maestro_locales para tener el nombre del local
        $this->metros = Metro::join('maestro_locales', 'metros.local_id', '=', 'maestro_locales.id')
            ->select('metros.*', 'maestro_locales.nombre_local')
            ->orderBy('maestro_locales.nombre_local', 'asc')
            ->orderBy('metros.numeroMetro', 'asc')
            ->get();
    }

    public function guardar()
    {
        $this->validate([
            'local_id' => 'required|integer',
            'numeroMetro' => 'required|string',
            'estado' => 'required|boolean',
        ]);

        // Verifica que el pasillo no exista YA en esa sucursal específica
        $existe = Metro::where('local_id', $this->local_id)
                       ->where('numeroMetro', $this->numeroMetro)
                       ->first();
        
        if($existe) {
            $this->addError('numeroMetro', 'Este pasillo ya existe en la sucursal seleccionada.');
            return;
        }

        Metro::create([
            'numeroMetro' => $this->numeroMetro,
            'estado' => $this->estado,
            'local_id' => $this->local_id,
        ]);

        // Limpia el numero, pero deja la sucursal seleccionada 
        // para facilitar la creación rápida de múltiples pasillos en un mismo local.
        $this->reset(['numeroMetro']); 
        $this->cargarMetros();
        
        session()->flash('mensaje', 'Metro o pasillo creado con éxito.');
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

    public function render()
    {
        return view('livewire.gestion-metros');
    }
}