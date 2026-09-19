<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $accion = $request->string('accion')->toString();
        $modelo = $request->string('modelo')->toString();

        $auditorias = Auditoria::query()
            ->when($accion, fn ($q) => $q->where('accion', $accion))
            ->when($modelo, fn ($q) => $q->where('modelo', $modelo))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $modelos = Auditoria::query()->distinct()->orderBy('modelo')->pluck('modelo');

        return view('auditoria.index', [
            'auditorias' => $auditorias,
            'accion' => $accion,
            'modelo' => $modelo,
            'acciones' => Auditoria::ACCIONES,
            'modelos' => $modelos,
        ]);
    }
}
