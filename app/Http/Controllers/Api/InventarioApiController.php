<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class InventarioApiController extends Controller
{
    /**
     * Retorna los inventarios en estado "Abierto" (1) para un local específico
     */
    public function getInventariosAbiertos($codLocal)
    {
        // Consulta la tabla corporativa en la base de datos principal
        $inventarios = DB::table('Inventario')
                         ->where('codLocal', $codLocal)
                         ->where('estado', 1) // 1 = Abierto
                         ->select('id', 'inventario as titulo', 'fecha', 'observacion')
                         ->orderBy('id', 'desc')
                         ->get();

        return response()->json([
            'status' => 'success',
            'data' => $inventarios
        ], 200);
    }

    /**
     * Retorna el catálogo maestro de productos para la base de datos local de Flutter
     */
    public function getProductos()
    {
        try {
            // Utiliza la conexión secundaria que configuramos en el .env
            $productos = DB::connection('sqlsrv_maestra')
                           ->table('productos')
                           ->select('codigo', 'descripcion') 
                           ->get();

            return response()->json([
                'status' => 'success',
                'total_registros' => $productos->count(),
                'data' => $productos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al conectar con la base de datos maestra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recibe el conteo físico desde Flutter, cruza el stock teórico y guarda en SQL Server
     */
    public function sincronizar(Request $request)
    {
        // 1. Validar la estructura del JSON que enviará Flutter
        $request->validate([
            'inventario_id' => 'required|integer',
            'user_id'       => 'required|integer',
            'conteo'        => 'required|array',
            'conteo.*.codigo_producto' => 'required|string',
            'conteo.*.cantidad'        => 'required|integer|min:1',
        ]);

        $inventarioId = $request->inventario_id;
        $userId = $request->user_id;
        $conteoFisico = $request->conteo;

        try {
            // Inicia una transacción por seguridad
            DB::beginTransaction();

            foreach ($conteoFisico as $item) {
                
                // 2. Consultar el stock teórico en la base de datos maestra
                $productoMaestro = DB::connection('sqlsrv_maestra')
                                     ->table('productos')
                                     ->where('codigo', $item['codigo_producto'])
                                     ->first();
                
                // Si el producto existe tomamos su stock, si no, asumimos 0
                $stockSistema = $productoMaestro ? $productoMaestro->stock_sistema : 0;
                $descripcion = $productoMaestro ? $productoMaestro->descripcion : 'Producto sin descripción';

                // 3. Guardar el choque de realidad en la tabla corporativa
                DB::table('inventario_conteo')->insert([
                    'inventario_id'        => $inventarioId,
                    'user_id'              => $userId,
                    'codigo_producto'      => $item['codigo_producto'],
                    'descripcion_producto' => $descripcion,
                    'stock_sistema'        => $stockSistema,
                    'conteo_fisico'        => $item['cantidad'],
                    'created_at'           => now(), 
                ]);
            }

            // Si todo salió bien, confirmamos los cambios en la base de datos
            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Sincronización completada con éxito. Los datos ya están en el sistema central.'
            ], 200);

        } catch (\Exception $e) {
            // Si hay cualquier error (ej. se cae SQL Server), revertimos todo para no dejar datos a medias
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Error crítico al sincronizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valida las credenciales del operario desde la app móvil
     */
    public function login(Request $request)
    {
        // 1. Validamos que envíen el RUT y la contraseña
        $request->validate([
            'rut_usuario' => 'required|string',
            'password'    => 'required|string',
        ]);

        // 2. Buscamos al usuario en la base de datos
        $user = User::where('rut_usuario', $request->rut_usuario)->first();

        // 3. Verificamos que exista y que la contraseña coincida
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'RUT o contraseña incorrectos.'
            ], 401);
        }

        // 4. Verificamos que el usuario tenga permisos de operario
        if ($user->tipo !== 'Operario' && $user->tipo !== 'Admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'No tienes permisos para acceder a la aplicación móvil.'
            ], 403);
        }

        // 5. Retornamos la información vital para que Flutter la guarde localmente
        return response()->json([
            'status'  => 'success',
            'message' => 'Login exitoso',
            'data'    => [
                'id'          => $user->id,
                'name'        => $user->name,
                'rut_usuario' => $user->rut_usuario,
                'tipo'        => $user->tipo
            ]
        ], 200);
    }
}