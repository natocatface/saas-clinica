<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Plan;
use App\Models\Suscripcion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuscripcionController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->string('estado')->toString();

        $suscripciones = Suscripcion::with(['clinica', 'plan'])
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->orderByDesc('id')->paginate(10)->withQueryString();

        $resumen = [
            'activas' => Suscripcion::where('estado', 'activa')->count(),
            'mrr' => Suscripcion::where('estado', 'activa')->where('ciclo', 'mensual')->sum('monto'),
            'total' => Suscripcion::count(),
        ];

        return view('superadmin.suscripciones.index', [
            'suscripciones' => $suscripciones, 'estado' => $estado,
            'estados' => Suscripcion::ESTADOS, 'resumen' => $resumen,
        ]);
    }

    public function create()
    {
        return view('superadmin.suscripciones.create', $this->formData(new Suscripcion(['estado' => 'activa', 'ciclo' => 'mensual'])));
    }

    public function store(Request $request)
    {
        Suscripcion::create($this->validated($request));

        return redirect()->route('superadmin.suscripciones.index')->with('ok', 'Suscripcion creada.');
    }

    public function edit(Suscripcion $suscripcion)
    {
        return view('superadmin.suscripciones.edit', $this->formData($suscripcion));
    }

    public function update(Request $request, Suscripcion $suscripcion)
    {
        $suscripcion->update($this->validated($request));

        return redirect()->route('superadmin.suscripciones.index')->with('ok', 'Suscripcion actualizada.');
    }

    public function destroy(Suscripcion $suscripcion)
    {
        $suscripcion->delete();

        return redirect()->route('superadmin.suscripciones.index')->with('ok', 'Suscripcion eliminada.');
    }

    private function formData(Suscripcion $suscripcion): array
    {
        return [
            'suscripcion' => $suscripcion,
            'clinicas' => Clinica::orderBy('nombre')->get(),
            'planes' => Plan::orderBy('precio_mensual')->get(),
            'estados' => Suscripcion::ESTADOS,
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'clinica_id' => ['required', 'exists:clinicas,id'],
            'plan_id' => ['nullable', 'exists:planes,id'],
            'estado' => ['required', Rule::in(array_keys(Suscripcion::ESTADOS))],
            'monto' => ['required', 'numeric', 'min:0'],
            'ciclo' => ['required', 'in:mensual,anual'],
            'inicio' => ['required', 'date'],
            'fin' => ['nullable', 'date', 'after_or_equal:inicio'],
        ]);
    }
}
