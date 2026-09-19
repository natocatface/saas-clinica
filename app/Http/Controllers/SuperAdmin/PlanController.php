<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $planes = Plan::withCount('clinicas')->orderBy('precio_mensual')->paginate(12);

        return view('superadmin.planes.index', compact('planes'));
    }

    public function create()
    {
        return view('superadmin.planes.create', ['plan' => new Plan()]);
    }

    public function store(Request $request)
    {
        Plan::create($this->validated($request));

        return redirect()->route('superadmin.planes.index')->with('ok', 'Plan creado.');
    }

    public function edit(Plan $plan)
    {
        return view('superadmin.planes.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->validated($request));

        return redirect()->route('superadmin.planes.index')->with('ok', 'Plan actualizado.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('superadmin.planes.index')->with('ok', 'Plan eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'precio_mensual' => ['required', 'numeric', 'min:0'],
            'max_usuarios' => ['required', 'integer', 'min:1'],
            'max_pacientes' => ['required', 'integer', 'min:1'],
            'caracteristicas' => ['nullable', 'string'],
            'destacado' => ['nullable', 'boolean'],
            'activo' => ['nullable', 'boolean'],
        ]) + ['destacado' => $request->boolean('destacado'), 'activo' => $request->boolean('activo')];
    }
}
