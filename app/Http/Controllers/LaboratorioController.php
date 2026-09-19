<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\OrdenLaboratorio;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaboratorioController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $estado = $request->string('estado')->toString();

        $ordenes = OrdenLaboratorio::query()
            ->with(['paciente', 'medico'])
            ->withCount('items')
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->when($q, function ($query) use ($q) {
                $query->where('numero', 'like', "%{$q}%")
                    ->orWhereHas('paciente', function ($p) use ($q) {
                        $p->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%");
                    });
            })
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('laboratorio.index', [
            'ordenes' => $ordenes, 'q' => $q, 'estado' => $estado, 'estados' => OrdenLaboratorio::ESTADOS,
        ]);
    }

    public function create()
    {
        return view('laboratorio.create', $this->formData(new OrdenLaboratorio()));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            $orden = OrdenLaboratorio::create(array_merge($data['orden'], ['numero' => OrdenLaboratorio::siguienteNumero()]));
            $orden->items()->createMany($data['items']);
        });

        return redirect()->route('laboratorio.index')->with('ok', 'Orden de laboratorio creada.');
    }

    public function show(OrdenLaboratorio $laboratorio)
    {
        $laboratorio->load(['paciente', 'medico', 'items']);

        return view('laboratorio.show', ['orden' => $laboratorio]);
    }

    public function edit(OrdenLaboratorio $laboratorio)
    {
        $laboratorio->load('items');

        return view('laboratorio.edit', $this->formData($laboratorio));
    }

    public function update(Request $request, OrdenLaboratorio $laboratorio)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($laboratorio, $data) {
            $laboratorio->update($data['orden']);
            $laboratorio->items()->delete();
            $laboratorio->items()->createMany($data['items']);
        });

        return redirect()->route('laboratorio.index')->with('ok', 'Orden de laboratorio actualizada.');
    }

    public function destroy(OrdenLaboratorio $laboratorio)
    {
        $laboratorio->delete();

        return redirect()->route('laboratorio.index')->with('ok', 'Orden eliminada.');
    }

    private function formData(OrdenLaboratorio $orden): array
    {
        return [
            'orden' => $orden,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::orderBy('apellidos')->get(),
            'estados' => OrdenLaboratorio::ESTADOS,
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:medicos,id'],
            'fecha' => ['required', 'date'],
            'estado' => ['required', 'in:'.implode(',', array_keys(OrdenLaboratorio::ESTADOS))],
            'observaciones' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.examen' => ['nullable', 'string', 'max:255'],
            'items.*.resultado' => ['nullable', 'string', 'max:255'],
            'items.*.unidad' => ['nullable', 'string', 'max:50'],
            'items.*.valor_referencia' => ['nullable', 'string', 'max:100'],
        ]);

        $items = collect($validated['items'])
            ->filter(fn ($i) => ! empty($i['examen']))
            ->map(fn ($i) => [
                'examen' => $i['examen'],
                'resultado' => $i['resultado'] ?? null,
                'unidad' => $i['unidad'] ?? null,
                'valor_referencia' => $i['valor_referencia'] ?? null,
            ])->values()->all();

        if (empty($items)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['items' => 'Agrega al menos un examen.']);
        }

        return [
            'orden' => [
                'paciente_id' => $validated['paciente_id'],
                'medico_id' => $validated['medico_id'] ?? null,
                'fecha' => $validated['fecha'],
                'estado' => $validated['estado'],
                'observaciones' => $validated['observaciones'] ?? null,
            ],
            'items' => $items,
        ];
    }
}
