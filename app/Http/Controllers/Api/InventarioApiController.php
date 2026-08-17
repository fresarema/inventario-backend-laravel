<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Inventario;

class InventarioApiController extends Controller
{
    // 1. LOGIN DE FLUTTER
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['mensaje' => 'Las credenciales son incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // 1. Busca en tabla relacional 'user_sucursal' los locales asignados a este operario
        $localesDelUsuario = \Illuminate\Support\Facades\DB::table('user_sucursal')
                                ->where('user_id', $user->id)
                                ->pluck('sucursal_id');

        // 2. Filtra los inventarios abiertos (estado 1) que pertenezcan EXCLUSIVAMENTE a los locales de este usuario
        $inventariosActivos = Inventario::where('estado', 1)
                                        ->whereIn('codLocal', $localesDelUsuario)
                                        ->get();

        return response()->json([
            'token' => $token,
            'usuario' => $user->name,
            'inventarios_activos' => $inventariosActivos 
        ], 200);
    }

    // 2. CATÁLOGO DUAL
    public function getProductos()
    {
        try {
            $productos = DB::connection('sqlsrv_maestra')
                           ->table('productos')
                           ->select('codigo', 'descripcion') 
                           ->get();

            return response()->json([
                'status' => 'success',
                'data' => $productos 
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 3. SINCRONIZACIÓN MAESTRA CON CRUCE DE STOCK
public function sincronizar(Request $request)
    {
        $request->validate([
            'inventario_id' => 'required|integer',
            'metro' => 'required|string',
            'conteos' => 'required|array',
            'conteos.*.codigo' => 'required|string',
            'conteos.*.cantidad' => 'required|numeric',
        ]);

        $inventarioId = $request->inventario_id;
        $userId = $request->user()->id; 
        $conteoFisico = $request->conteos;
        $numeroMetroEnviado = $request->metro;

        try {
            DB::beginTransaction();

            // 1. Busca el metro en la tabla 'metros' usando el número que digitó el operario
            $metroRecord = DB::table('metros')->where('numeroMetro', $numeroMetroEnviado)->first();

            // 2. Si el metro no existe en la BD, devolvemos un error para que la app se lo muestre al operario
            if (!$metroRecord) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'El Metro N° ' . $numeroMetroEnviado . ' no existe en la base de datos.'
                ], 400);
            }

            // 3. Logica de metros abiertos y cerrados
            
            if ($metroRecord->estado != 1) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'El Metro N° ' . $numeroMetroEnviado . ' está cerrado y no acepta más registros.'
                ], 400);
            }
            

            // 4. Captura el ID real para la llave foránea
            $metroIdCorrecto = $metroRecord->id;
            // ------------------------------

            foreach ($conteoFisico as $item) {
                // Consulta stock teórico a la base maestra
                $productoMaestro = DB::connection('sqlsrv_maestra')
                                     ->table('productos')
                                     ->where('codigo', $item['codigo'])
                                     ->first();
                
                $stockSistema = $productoMaestro ? $productoMaestro->stock_sistema : 0;
                $descripcion = $productoMaestro ? $productoMaestro->descripcion : 'Producto sin descripción';

                // Inserción en tabla corporativa con las variables cruzadas
                DB::table('inventario_conteo')->insert([
                    'inventario_id'        => $inventarioId,
                    'user_id'              => $userId,
                    'metro_id'             => $metroIdCorrecto, 
                    'codigo_producto'      => $item['codigo'],
                    'descripcion_producto' => $descripcion,
                    'stock_sistema'        => $stockSistema,
                    'conteo_fisico'        => $item['cantidad'],
                    'created_at'           => now(), 
                ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Sincronización completada con éxito.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}