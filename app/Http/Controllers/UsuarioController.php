<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Inventario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\MaestroLocal;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('sucursales')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        // Rescata los locales desde el catálogo maestro
        $sucursales = MaestroLocal::orderBy('nombre_local')->get();

        return view('usuarios.create', compact('sucursales'));
    }

    public function store(Request $request)
    {
        // 1. Valida los datos que llegan del formulario
        $request->validate([
            'name'        => 'required|string|max:255',
            'rut_usuario' => 'required|string|max:12',
            'email'       => 'required|email|unique:user,email', // Valida contra la tabla 'user'
            'password'    => 'required|string|min:6',
            'tipo'        => 'required'
        ]);

        // 2. Crea el usuario encriptando la contraseña
        $user = new User();
        $user->name        = $request->name;
        $user->rut_usuario = $request->rut_usuario;
        $user->email       = $request->email;
        $user->password    = Hash::make($request->password);
        $user->tipo        = $request->tipo;
        $user->save();

        // 3. Guarda la relación en la tabla user_sucursal
        if ($request->has('sucursales')) {
            foreach ($request->sucursales as $codLocal) {
                DB::table('user_sucursal')->insert([
                    'user_id'     => $user->id,
                    'sucursal_id' => $codLocal
                ]);
            }
        }

        // 4. Redirige de vuelta al listado
        return redirect()->route('usuarios.index')
                         ->with('success', 'El usuario fue registrado exitosamente.');
    }


    public function edit($id)
    {
        $usuario = User::with('sucursales')->findOrFail($id);
        $sucursales = MaestroLocal::orderBy('nombre_local')->get();
        
        // Crea un arreglo simple con los IDs de las sucursales asignadas para marcarlas en el checkbox
        $sucursalesAsignadas = $usuario->sucursales->pluck('codLocal')->toArray();

        return view('usuarios.edit', compact('usuario', 'sucursales', 'sucursalesAsignadas'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'rut_usuario' => 'required|string|max:12',
            'email'       => 'required|email|unique:user,email,' . $id, // Ignora el propio email del usuario
            'tipo'        => 'required'
        ]);

        $usuario->name        = $request->name;
        $usuario->rut_usuario = $request->rut_usuario;
        $usuario->email       = $request->email;
        $usuario->tipo        = $request->tipo;
        
        // Solo actualiza la contraseña si se escribio algo nuevo
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        $usuario->save();

        // Limpia los locales antiguos y guardamos los nuevos
        DB::table('user_sucursal')->where('user_id', $id)->delete();
        
        if ($request->has('sucursales')) {
            foreach ($request->sucursales as $codLocal) {
                DB::table('user_sucursal')->insert([
                    'user_id'     => $id,
                    'sucursal_id' => $codLocal
                ]);
            }
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        // Primero borra las relaciones en la tabla pivote para evitar errores de llave foránea
        DB::table('user_sucursal')->where('user_id', $id)->delete();
        
        // Luego borra al usuario
        User::destroy($id);

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado del sistema.');
    }
}