<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecetaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $recetas = Receta::query()
            ->with(['paciente', 'medico'])
            ->withCount('items')
            ->when($q, function ($query) use ($q) {
                $query->whereHas('paciente', function ($p) use ($q) {
                    $p->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('recetas.index', compact('recetas', 'q'));
    }

    public function create()
    {
        return view('recetas.create', $this->formData(new Receta()));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $receta = Receta::create($data['receta']);
            $receta->items()->createMany($data['items']);
        });

        return redirect()->route('recetas.index')->with('ok', 'Receta emitida correctamente.');
    }

    public function show(Receta $receta)
    {
        $receta->load(['paciente', 'medico', 'items']);

        return view('recetas.show', compact('receta'));
    }

    public function edit(Receta $receta)
    {
        $receta->load('items');

        return view('recetas.edit', $this->formData($receta));
    }

    public function update(Request $request, Receta $receta)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($receta, $data) {
            $receta->update($data['receta']);
            $receta->items()->delete();
            $receta->items()->createMany($data['items']);
        });

        return redirect()->route('recetas.index')->with('ok', 'Receta actualizada.');
    }

    public function destroy(Receta $receta)
    {
        $receta->delete();

        return redirect()->route('recetas.index')->with('ok', 'Receta eliminada.');
    }

    private function formData(Receta $receta): array
    {
        return [
            'receta' => $receta,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::orderBy('apellidos')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:medicos,id'],
            'fecha' => ['required', 'date'],
            'diagnostico' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicamento' => ['nullable', 'string', 'max:255'],
            'items.*.dosis' => ['nullable', 'string', 'max:255'],
            'items.*.frecuencia' => ['nullable', 'string', 'max:255'],
            'items.*.duracion' => ['nullable', 'string', 'max:255'],
            'items.*.indicaciones' => ['nullable', 'string', 'max:255'],
        ]);

        // Conservar solo filas con medicamento
        $items = collect($validated['items'])
            ->filter(fn ($i) => ! empty($i['medicamento']))
            ->map(fn ($i) => [
                'medicamento' => $i['medicamento'],
                'dosis' => $i['dosis'] ?? null,
                'frecuencia' => $i['frecuencia'] ?? null,
                'duracion' => $i['duracion'] ?? null,
                'indicaciones' => $i['indicaciones'] ?? null,
            ])
            ->values()
            ->all();

        if (empty($items)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['items' => 'Agrega al menos un medicamento.']);
        }

        return [
            'receta' => [
                'paciente_id' => $validated['paciente_id'],
                'medico_id' => $validated['medico_id'] ?? null,
                'fecha' => $validated['fecha'],
                'diagnostico' => $validated['diagnostico'] ?? null,
                'notas' => $validated['notas'] ?? null,
            ],
            'items' => $items,
        ];
    }
}
