<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\Medico;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $medicos = Medico::query()
            ->with('especialidad')
            ->when($q, function ($query) use ($q) {
                $query->where('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('matricula', 'like', "%{$q}%");
            })
            ->orderBy('apellidos')
            ->paginate(10)
            ->withQueryString();

        return view('medicos.index', compact('medicos', 'q'));
    }

    public function create()
    {
        return view('medicos.create', [
            'medico' => new Medico(),
            'especialidades' => Especialidad::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Medico::create($this->validated($request));

        return redirect()->route('medicos.index')->with('ok', 'Medico registrado correctamente.');
    }

    public function edit(Medico $medico)
    {
        return view('medicos.edit', [
            'medico' => $medico,
            'especialidades' => Especialidad::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Medico $medico)
    {
        $medico->update($this->validated($request));

        return redirect()->route('medicos.index')->with('ok', 'Medico actualizado.');
    }

    public function destroy(Medico $medico)
    {
        $medico->delete();

        return redirect()->route('medicos.index')->with('ok', 'Medico eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'matricula' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'hora_inicio' => ['nullable', 'date_format:H:i'],
            'hora_fin' => ['nullable', 'date_format:H:i'],
            'activo' => ['nullable', 'boolean'],
        ]) + ['activo' => $request->boolean('activo')];
    }
}
