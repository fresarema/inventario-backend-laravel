<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\MaestroLocal;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 1. KPI: Cuenta cuántos inventarios están en estado Abierto (1)
        $inventariosActivos = Inventario::where('estado', 1)->count();

        // 2. KPI: Cuenta en cuántos locales distintos se están haciendo inventarios ahora
        $localesEnProceso = Inventario::where('estado', 1)->distinct('codLocal')->count('codLocal');

        // 3. KPI: Obtiene la fecha/hora del último escaneo registrado en la base de datos
        $ultimaSincronizacion = DB::table('inventario_conteo')->max('created_at');

        // 4. Catálogo: Trae los locales para poblar el primer selector del filtro
        $locales = MaestroLocal::orderBy('nombre_local')->get();

        // Envia todas estas variables a la vista
        return view('home', compact('inventariosActivos', 'localesEnProceso', 'ultimaSincronizacion', 'locales'));
    }
}
