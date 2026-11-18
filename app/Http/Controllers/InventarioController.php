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