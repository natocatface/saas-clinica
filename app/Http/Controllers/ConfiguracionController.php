<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public const CAMPOS = [
        'clinica_nombre', 'clinica_nit', 'clinica_direccion', 'clinica_telefono',
        'clinica_email', 'clinica_ciudad', 'moneda', 'mensaje_pie',
    ];

    public function index()
    {
        $config = [];
        foreach (self::CAMPOS as $campo) {
            $config[$campo] = Configuracion::get($campo);
        }

        return view('configuracion.index', compact('config'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'clinica_nombre' => ['nullable', 'string', 'max:255'],
            'clinica_nit' => ['nullable', 'string', 'max:100'],
            'clinica_direccion' => ['nullable', 'string', 'max:255'],
            'clinica_telefono' => ['nullable', 'string', 'max:50'],
            'clinica_email' => ['nullable', 'email', 'max:255'],
            'clinica_ciudad' => ['nullable', 'string', 'max:100'],
            'moneda' => ['nullable', 'string', 'max:10'],
            'mensaje_pie' => ['nullable', 'string', 'max:255'],
        ]);

        foreach (self::CAMPOS as $campo) {
            Configuracion::set($campo, $data[$campo] ?? null);
        }

        return redirect()->route('configuracion.index')->with('ok', 'Configuracion guardada correctamente.');
    }
}
