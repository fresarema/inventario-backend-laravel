<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Inventario;
use App\Models\MaestroLocal;
use Illuminate\Support\Facades\Auth;

class InventarioComponent extends Component
{
    // Variables del formulario
    public $inventario_id;
    public $titulo_inventario; // Corresponde al campo 'inventario' en la BD
    public $codLocal;
    public $fecha;
    public $observacion;
    
    public $modalTitle = 'Abrir Nuevo Inventario';

    public function render()
    {
        $inventarios = Inventario::orderBy('id', 'desc')->get();
        $locales = MaestroLocal::orderBy('nombre_local')->get();
        
        return view('livewire.inventario-component', compact('inventarios', 'locales'));
    }

    public function limpiarCampos()
    {
        $this->inventario_id = null;
        $this->titulo_inventario = '';
        $this->codLocal = '';
        $this->fecha = date('Y-m-d'); // Fecha actual por defecto
        $this->observacion = '';
        $this->modalTitle = 'Abrir Nuevo Inventario';
        $this->resetErrorBag();
    }

    public function abrirModalNuevo()
    {
        $this->limpiarCampos();
        $this->dispatch('abrir-modal');
    }

    public function editar($id)
    {
        $this->limpiarCampos();
        $inv = Inventario::findOrFail($id);
        
        $this->inventario_id = $inv->id;
        $this->titulo_inventario = $inv->inventario;
        $this->codLocal = $inv->codLocal;
        $this->fecha = $inv->fecha;
        $this->observacion = $inv->observacion;
        
        $this->modalTitle = 'Editar Inventario';
        $this->dispatch('abrir-modal');
    }

    public function guardar()
    {
        $this->validate([
            'titulo_inventario' => 'required|string|max:255',
            'codLocal'          => 'required|string',
            'fecha'             => 'required|date',
            'observacion'       => 'nullable|string'
        ]);

        // Extraer el nombre del local
        $localMaestro = MaestroLocal::where('codLocal', $this->codLocal)->first();

        Inventario::updateOrCreate(
            ['id' => $this->inventario_id],
            [
                'inventario'   => $this->titulo_inventario,
                'codLocal'     => $this->codLocal,
                'nombre_local' => $localMaestro->nombre_local,
                'fecha'        => $this->fecha,
                'observacion'  => $this->observacion,
                'user_id'      => Auth::id() ?? 1,
                // Si es nuevo, estado 1. Si se está editando, mantiene el estado que tenía
                'estado'       => $this->inventario_id ? Inventario::find($this->inventario_id)->estado : 1,
            ]
        );

        $this->dispatch('cerrar-modal');
        $this->dispatch('alerta-exito', mensaje: 'Proceso de inventario guardado correctamente.');
    }

    // Unifica el cierre y apertura en una sola función "Toggle"
    public function cambiarEstado($id, $nuevoEstado)
    {
        $inv = Inventario::findOrFail($id);
        $inv->estado = $nuevoEstado;
        $inv->save();

        $mensaje = $nuevoEstado == 0 ? 'Inventario cerrado correctamente.' : 'Inventario reabierto correctamente.';
        $this->dispatch('alerta-exito', mensaje: $mensaje);
    }
}