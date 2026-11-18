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

}