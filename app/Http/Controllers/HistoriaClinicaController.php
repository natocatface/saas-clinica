<?php

namespace App\Http\Controllers;

use App\Models\HistoriaArchivo;
use App\Models\HistoriaClinica;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HistoriaClinicaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $historias = HistoriaClinica::query()
            ->with(['paciente', 'medico'])
            ->when($q, function ($query) use ($q) {
                $query->whereHas('paciente', function ($p) use ($q) {
                    $p->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%")->orWhere('ci', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('historias.index', compact('historias', 'q'));
    }

    public function create(Request $request)
    {
        $historia = new HistoriaClinica(['paciente_id' => $request->integer('paciente_id') ?: null]);

        return view('historias.create', $this->formData($historia));
    }

    public function store(Request $request)
    {
        HistoriaClinica::create($this->validated($request));

        return redirect()->route('historias.index')->with('ok', 'Historia clinica registrada.');
    }

    public function show(HistoriaClinica $historia)
    {
        $historia->load(['paciente', 'medico', 'archivos']);

        return view('historias.show', compact('historia'));
    }

    public function storeArchivo(Request $request, HistoriaClinica $historia)
    {
        $request->validate([
            'archivo' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx'],
        ], [], ['archivo' => 'archivo']);

        $file = $request->file('archivo');
        $ruta = $file->store('historias/'.$historia->id, 'public');

        $historia->archivos()->create([
            'nombre' => $file->getClientOriginalName(),
            'ruta' => $ruta,
            'mime' => $file->getClientMimeType(),
            'tamano' => $file->getSize(),
        ]);

        return back()->with('ok', 'Archivo adjuntado correctamente.');
    }

    public function destroyArchivo(HistoriaClinica $historia, HistoriaArchivo $archivo)
    {
        abort_unless($archivo->historia_clinica_id === $historia->id, 404);

        Storage::disk('public')->delete($archivo->ruta);
        $archivo->delete();

        return back()->with('ok', 'Archivo eliminado.');
    }

    public function edit(HistoriaClinica $historia)
    {
        return view('historias.edit', $this->formData($historia));
    }

    public function update(Request $request, HistoriaClinica $historia)
    {
        $historia->update($this->validated($request));

        return redirect()->route('historias.index')->with('ok', 'Historia clinica actualizada.');
    }

    public function destroy(HistoriaClinica $historia)
    {
        $historia->delete();

        return redirect()->route('historias.index')->with('ok', 'Historia clinica eliminada.');
    }

    private function formData(HistoriaClinica $historia): array
    {
        return [
            'historia' => $historia,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::orderBy('apellidos')->get(),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['nullable', 'exists:medicos,id'],
            'fecha' => ['required', 'date'],
            'motivo_consulta' => ['nullable', 'string', 'max:255'],
            'sintomas' => ['nullable', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'tratamiento' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'peso' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'talla' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'temperatura' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'frecuencia_cardiaca' => ['nullable', 'integer', 'min:0', 'max:400'],
        ]);
    }
}
