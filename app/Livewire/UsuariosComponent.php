<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\MaestroLocal;
use Illuminate\Support\Facades\Hash;

class UsuariosComponent extends Component
{
    // Variables del formulario
    public $name, $rut_usuario, $email, $password, $tipo = 'Operario';
    public $sucursalesSeleccionadas = [];
    
    // Variables de control
    public $usuario_id;
    public $modalTitle = 'Crear Nuevo Usuario';

    public function render()
    {
        $usuarios = User::with('sucursales')->get();
        $sucursales = MaestroLocal::orderBy('nombre_local')->get();
        
        return view('livewire.usuarios-component', compact('usuarios', 'sucursales'));
    }

    public function limpiarCampos()
    {
        $this->name = '';
        $this->rut_usuario = '';
        $this->email = '';
        $this->password = '';
        $this->tipo = 'Operario';
        $this->sucursalesSeleccionadas = [];
        $this->usuario_id = null;
        $this->modalTitle = 'Crear Nuevo Usuario';
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
        $usuario = User::with('sucursales')->findOrFail($id);
        
        $this->usuario_id = $usuario->id;
        $this->name = $usuario->name;
        $this->rut_usuario = $usuario->rut_usuario;
        $this->email = $usuario->email;
        $this->tipo = $usuario->tipo;
        $this->sucursalesSeleccionadas = $usuario->sucursales->pluck('codLocal')->map(fn($id) => (string)$id)->toArray();
        
        $this->modalTitle = 'Editar Usuario';
        $this->dispatch('abrir-modal');
    }

    public function guardar()
    {
        $reglas = [
            'name' => 'required|string|max:255',
            'rut_usuario' => 'required|string|max:12',
            'email' => 'required|email|unique:user,email,' . $this->usuario_id,
            'tipo' => 'required'
        ];

        // 1. Validar contraseña solo si es nuevo o si escribió algo al editar
        if (!$this->usuario_id || !empty($this->password)) {
            $reglas['password'] = 'required|string|min:6';
        }

        $this->validate($reglas);

        // 2. Prepara el arreglo con los datos obligatorios
        $datosGuardar = [
            'name' => $this->name,
            'rut_usuario' => $this->rut_usuario,
            'email' => $this->email,
            'tipo' => $this->tipo,
        ];

        // 3. Si hay contraseña en el formulario, la encripta y la agrega al arreglo
        if (!empty($this->password)) {
            $datosGuardar['password'] = Hash::make($this->password);
        }

        // 4. Lanza el guardado a SQL Server con el paquete completo
        $usuario = User::updateOrCreate(
            ['id' => $this->usuario_id],
            $datosGuardar
        );

        // Sincroniza las sucursales asignadas
        $usuario->sucursales()->sync($this->sucursalesSeleccionadas);

        $this->dispatch('cerrar-modal');
        $this->dispatch('alerta-exito', mensaje: 'Usuario guardado correctamente.');
    }

    public function eliminar($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->sucursales()->detach();
        $usuario->delete();
        
        $this->dispatch('alerta-exito', mensaje: 'Usuario eliminado del sistema.');
    }
}