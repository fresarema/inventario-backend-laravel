<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\MaestroLocal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index()
    {
        // Trae todos los inventarios ordenados por los más recientes
        $inventarios = Inventario::orderBy('id', 'desc')->get();
        return view('inventarios.index', compact('inventarios'));
    }

    public function create()
    {
        $locales = MaestroLocal::orderBy('nombre_local')->get();
        
        return view('inventarios.create', compact('locales'));
    }

    public function store(Request $request)
    {
        // 1. Validar (quitamos user_id de la validación)
        $request->validate([
            'inventario'  => 'required|string|max:255',
            'codLocal'    => 'required|string',
            'fecha'       => 'required|date',
            'observacion' => 'nullable|string'
        ]);

        // 2. Extraer el nombre del local
        $localMaestro = MaestroLocal::where('codLocal', $request->codLocal)->first();

        // 3. Crear el registro
        $inventario = new Inventario();
        
        // Registramos al administrador que crea el proceso (o el ID 1 por defecto)
        $inventario->user_id      = auth()->id() ?? 1; 
        
        $inventario->inventario   = $request->inventario;
        $inventario->codLocal     = $request->codLocal;
        $inventario->nombre_local = $localMaestro->nombre_local; 
        $inventario->fecha        = $request->fecha;
        $inventario->observacion  = $request->observacion;
        // Asigna 1 asumiendo que es el código numérico para "Abierto" en la BD corporativa
        $inventario->estado       = 1; 
        $inventario->save();

        // 4. Redirigir
        return redirect()->route('inventarios.index')
                         ->with('success', 'El proceso de inventario ha sido abierto exitosamente.');
    }

    public function cerrar($id)
    {
        $inventario = Inventario::findOrFail($id);
        
        // Cambia el estado a 0 (Cerrado)
        $inventario->estado = 0; 
        $inventario->save();

        return redirect()->route('inventarios.index')
                         ->with('success', 'El proceso de inventario ha sido cerrado exitosamente.');
    }

    public function show($id)
    {
        // Trae la cabecera del inventario
        $inventario = Inventario::findOrFail($id);

        // Consulta los registros escaneados en la tabla corporativa
        // (Si los nombres de las columnas en tu base de datos son distintos, ajústalos aquí)
        $conteos = DB::table('inventario_conteo')
                    ->where('inventario_id', $id)
                    ->orderBy('id', 'desc')
                    ->get();

        return view('inventarios.show', compact('inventario', 'conteos'));
    }
}