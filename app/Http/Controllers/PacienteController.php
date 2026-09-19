<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $pacientes = Paciente::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('ci', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pacientes.index', compact('pacientes', 'q'));
    }

    public function export(Request $request)
    {
        $q = $request->string('q')->toString();

        $pacientes = Paciente::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('ci', 'like', "%{$q}%")
                    ->orWhere('telefono', 'like', "%{$q}%");
            })
            ->orderBy('apellidos')->get();

        $filas = $pacientes->map(fn ($p) => [
            $p->nombres, $p->apellidos, $p->ci, $p->sexo,
            $p->edad, $p->telefono, $p->email, $p->grupo_sanguineo,
            $p->seguro, $p->activo ? 'Activo' : 'Inactivo',
        ]);

        return csv_descarga(
            'pacientes_'.now()->format('Ymd').'.csv',
            ['Nombres', 'Apellidos', 'CI', 'Sexo', 'Edad', 'Telefono', 'Email', 'Grupo sanguineo', 'Seguro', 'Estado'],
            $filas
        );
    }

    public function create()
    {
        return view('pacientes.create', ['paciente' => new Paciente()]);
    }

    public function store(Request $request)
    {
        Paciente::create($this->validated($request));

        return redirect()->route('pacientes.index')->with('ok', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load(['citas.medico', 'citas.especialidad']);

        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $paciente->update($this->validated($request));

        return redirect()->route('pacientes.index')->with('ok', 'Paciente actualizado.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();

        return redirect()->route('pacientes.index')->with('ok', 'Paciente eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'ci' => ['nullable', 'string', 'max:50'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'sexo' => ['nullable', 'in:M,F,O'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'grupo_sanguineo' => ['nullable', 'string', 'max:5'],
            'alergias' => ['nullable', 'string'],
            'seguro' => ['nullable', 'string', 'max:255'],
            'activo' => ['nullable', 'boolean'],
        ]) + ['activo' => $request->boolean('activo')];
    }
}
