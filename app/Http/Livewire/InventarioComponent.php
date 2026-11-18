<?php

namespace App\Http\Livewire; 
use Livewire\Component;
use App\Models\Inventario;
use App\Models\MaestroLocal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventarioComponent extends Component
{
    // Variables del formulario
    public $inventario_id;
    public $titulo_inventario; 
    public $codLocal;
    public $fecha;
    public $observacion;
    
    public $modalTitle = 'Abrir Nuevo Inventario';

    public function render()
    {
        // 1. Identifica al usuario logueado
        $userId = Auth::id();

        // 2. Extrae un arreglo con los códigos de los locales que tiene asignados
        $localesAsignados = DB::table('user_sucursal')
                              ->where('user_id', $userId)
                              ->pluck('sucursal_id');

        // 3. Filtra la tabla de inventarios usando whereIn
        $inventarios = Inventario::whereIn('codLocal', $localesAsignados)
                                 ->orderBy('id', 'desc')
                                 ->get();

        // 4. Filtra el select del modal para que solo pueda crear inventarios en sus locales
        $locales = MaestroLocal::whereIn('codLocal', $localesAsignados)
                               ->orderBy('nombre_local')
                               ->get();
        
        return view('livewire.inventario-component', compact('inventarios', 'locales'))
            ->extends('layouts.app')
            ->section('content');
    }

    public function limpiarCampos()
    {
        $this->inventario_id = null;
        $this->titulo_inventario = '';
        $this->codLocal = '';
        $this->fecha = date('Y-m-d'); 
        $this->observacion = '';
        $this->modalTitle = 'Abrir Nuevo Inventario';
        $this->resetErrorBag();
    }

    public function abrirModalNuevo()
    {
        $this->limpiarCampos();
        $this->dispatchBrowserEvent('abrir-modal');
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
        $this->dispatchBrowserEvent('abrir-modal');
    }

    public function guardar()
    {
        $this->validate([
            'titulo_inventario' => 'required|string|max:255',
            'codLocal'          => 'required|string',
            'fecha'             => 'required|date',
            'observacion'       => 'nullable|string'
        ]);

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
                'estado'       => $this->inventario_id ? Inventario::find($this->inventario_id)->estado : 1,
            ]
        );

        // Envío de eventos en Livewire 2 (usando arreglos para pasar datos)
        $this->dispatchBrowserEvent('cerrar-modal');
        $this->dispatchBrowserEvent('alerta-exito', ['mensaje' => 'Proceso de inventario guardado correctamente.']);
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        $inv = Inventario::findOrFail($id);
        $inv->estado = $nuevoEstado;
        $inv->save();

        $mensaje = $nuevoEstado == 0 ? 'Inventario cerrado correctamente.' : 'Inventario reabierto correctamente.';
        $this->dispatchBrowserEvent('alerta-exito', ['mensaje' => $mensaje]);
    }
}