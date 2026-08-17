<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Metro;

class GestionMetros extends Component
{
    public $metros;
    public $numeroMetro;
    
    // Por defecto, al crear un metro nuevo estará "Abierto" (1)
    public $estado = 1; 

    public function mount()
    {
        $this->cargarMetros();
    }

    public function cargarMetros()
    {
        // Trae todos los metros ordenados por su número
        $this->metros = Metro::orderBy('numeroMetro', 'asc')->get();
    }

    public function guardar()
    {
        $this->validate([
            'numeroMetro' => 'required|string|unique:metros,numeroMetro',
            'estado' => 'required|boolean',
        ]);

        Metro::create([
            'numeroMetro' => $this->numeroMetro,
            'estado' => $this->estado,
        ]);

        // Limpia el input después de guardar
        $this->reset(['numeroMetro']);
        
        // Recarga la lista
        $this->cargarMetros();
        
        session()->flash('mensaje', 'Metro o pasillo creado con éxito.');
    }

    public function cambiarEstado($id)
    {
        $metro = Metro::find($id);
        if ($metro) {
            // Si es 1 pasa a 0, si es 0 pasa a 1
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