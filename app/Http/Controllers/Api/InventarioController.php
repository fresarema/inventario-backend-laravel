<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Producto;

class InventarioController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validar que vengan los datos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Buscar al usuario
        $user = User::where('email', $request->email)->first();

        // 3. Verificar si existe y si la contraseña es correcta
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'mensaje' => 'Las credenciales son incorrectas'
            ], 401);
        }

        // 4. Generar el token para la app móvil
        $token = $user->createToken('token-app-movil')->plainTextToken;

        // 5. Devolver el token y los datos del usuario
        return response()->json([
            'mensaje' => 'Login exitoso',
            'token' => $token,
            'usuario' => $user->name
        ]);
    }

    public function productos()
    {
        // Trae solo las columnas necesarias para no saturar la memoria del teléfono
        $productos = Producto::select('codigo', 'descripcion')->get();
        
        return response()->json([
            'total' => $productos->count(),
            'data' => $productos
        ]);
    }

    public function sincronizar(Request $request)
    {
        // 1. Valida que el JSON venga con el formato correcto
        $request->validate([
            'metro' => 'required|string',
            'conteos' => 'required|array',
            'conteos.*.codigo' => 'required|string',
            'conteos.*.cantidad' => 'required|numeric',
        ]);

        $usuario = $request->user();

        // 2. Busca el inventario activo en la base de datos
        $inventarioActivo = \App\Models\Inventario::where('activo', true)->first();

        if (!$inventarioActivo) {
            return response()->json(['message' => 'No hay ningún inventario activo en este momento.'], 400);
        }

        // 3. Prepara los datos para insertarlos masivamente
        $registros = [];
        $ahora = now()->format('Y-m-d H:i:s'); // Formato seguro para SQL Server

        foreach ($request->conteos as $item) {
            $registros[] = [
                'inventario_id' => $inventarioActivo->id,
                'usuario_id' => $usuario->id,
                'metro' => $request->metro,
                'producto_codigo' => $item['codigo'],
                'cantidad' => $item['cantidad'],
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }

        // 4. Inserta todo en la tabla registro_conteos de un solo golpe
        \App\Models\RegistroConteo::insert($registros);

        return response()->json([
            'message' => 'Sincronización exitosa',
            'total_procesado' => count($registros)
        ], 200);
    }
}