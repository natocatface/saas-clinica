<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $especialidades = Especialidad::query()
            ->when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%"))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('especialidades.index', compact('especialidades', 'q'));
    }

    public function create()
    {
        return view('especialidades.create', ['especialidad' => new Especialidad()]);
    }

    public function store(Request $request)
    {
        Especialidad::create($this->validated($request));

        return redirect()->route('especialidades.index')->with('ok', 'Especialidad creada correctamente.');
    }

    public function edit(Especialidad $especialidade)
    {
        return view('especialidades.edit', ['especialidad' => $especialidade]);
    }

    public function update(Request $request, Especialidad $especialidade)
    {
        $especialidade->update($this->validated($request));

        return redirect()->route('especialidades.index')->with('ok', 'Especialidad actualizada.');
    }

    public function destroy(Especialidad $especialidade)
    {
        $especialidade->delete();

        return redirect()->route('especialidades.index')->with('ok', 'Especialidad eliminada.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'tarifa' => ['required', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:20'],
            'activo' => ['nullable', 'boolean'],
        ]) + ['activo' => $request->boolean('activo')];
    }
}
