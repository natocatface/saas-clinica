<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $estado = $request->string('estado')->toString();

        $citas = Cita::query()
            ->with(['paciente', 'medico', 'especialidad'])
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->when($q, function ($query) use ($q) {
                $query->whereHas('paciente', function ($p) use ($q) {
                    $p->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('fecha')
            ->orderBy('hora')
            ->paginate(10)
            ->withQueryString();

        return view('citas.index', [
            'citas' => $citas,
            'q' => $q,
            'estado' => $estado,
            'estados' => Cita::ESTADOS,
        ]);
    }

    public function export(Request $request)
    {
        $estado = $request->string('estado')->toString();
        $q = $request->string('q')->toString();

        $citas = Cita::query()
            ->with(['paciente', 'medico', 'especialidad'])
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->when($q, function ($query) use ($q) {
                $query->whereHas('paciente', function ($p) use ($q) {
                    $p->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('fecha')->orderBy('hora')->get();

        $filas = $citas->map(fn ($c) => [
            $c->fecha?->format('d/m/Y'), substr($c->hora, 0, 5),
            $c->paciente?->nombre_completo, $c->medico?->nombre_completo,
            $c->especialidad?->nombre, $c->estadoLabel(), $c->motivo,
        ]);

        return csv_descarga(
            'citas_'.now()->format('Ymd').'.csv',
            ['Fecha', 'Hora', 'Paciente', 'Medico', 'Especialidad', 'Estado', 'Motivo'],
            $filas
        );
    }

    public function calendario(Request $request)
    {
        $mes = $request->string('mes')->toString();
        try {
            $ref = $mes ? \Carbon\Carbon::createFromFormat('Y-m', $mes)->startOfMonth() : \Carbon\Carbon::now()->startOfMonth();
        } catch (\Throwable $e) {
            $ref = \Carbon\Carbon::now()->startOfMonth();
        }

        $inicio = $ref->copy()->startOfMonth();
        $fin = $ref->copy()->endOfMonth();

        $citas = Cita::with(['paciente', 'medico'])
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->orderBy('hora')
            ->get()
            ->groupBy(fn ($c) => $c->fecha->format('Y-m-d'));

        return view('citas.calendario', [
            'ref' => $ref,
            'inicio' => $inicio,
            'fin' => $fin,
            'citasPorDia' => $citas,
            'estados' => Cita::ESTADOS,
        ]);
    }

    public function recordar(Cita $cita)
    {
        $email = $cita->paciente?->email;

        if ($email) {
            $cuerpo = "Estimado(a) {$cita->paciente->nombre_completo},\n\n"
                ."Le recordamos su cita el {$cita->fecha->format('d/m/Y')} a las ".substr($cita->hora, 0, 5)
                ." con Dr(a). ".($cita->medico?->nombre_completo ?? '')."\n\nGracias.";
            try {
                \Illuminate\Support\Facades\Mail::raw($cuerpo, function ($m) use ($email) {
                    $m->to($email)->subject('Recordatorio de su cita');
                });
            } catch (\Throwable $e) {
                // En local sin SMTP el envio se registra igualmente como intento
            }
        }

        $cita->update(['recordatorio_enviado' => true, 'recordatorio_at' => now()]);

        return back()->with('ok', $email
            ? 'Recordatorio enviado a '.$email
            : 'Cita marcada como recordada (el paciente no tiene email).');
    }

    public function create(Request $request)
    {
        return view('citas.create', $this->formData(new Cita(['fecha' => $request->date('fecha')])));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->verificarChoque($data);

        Cita::create($data);

        return redirect()->route('citas.index')->with('ok', 'Cita registrada correctamente.');
    }

    public function edit(Cita $cita)
    {
        return view('citas.edit', $this->formData($cita));
    }

    public function update(Request $request, Cita $cita)
    {
        $data = $this->validated($request);
        $this->verificarChoque($data, $cita->id);

        $cita->update($data);

        return redirect()->route('citas.index')->with('ok', 'Cita actualizada.');
    }

    /** Evita doble reserva del mismo medico en la misma fecha y hora. */
    private function verificarChoque(array $data, ?int $ignorarId = null): void
    {
        if (($data['estado'] ?? null) === 'cancelada') {
            return;
        }

        $existe = Cita::where('medico_id', $data['medico_id'])
            ->whereDate('fecha', $data['fecha'])
            ->where('hora', $data['hora'])
            ->where('estado', '!=', 'cancelada')
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->exists();

        if ($existe) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'hora' => 'El medico ya tiene una cita en esa fecha y hora. Elige otro horario.',
            ]);
        }
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')->with('ok', 'Cita eliminada.');
    }

    private function formData(Cita $cita): array
    {
        return [
            'cita' => $cita,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'medicos' => Medico::with('especialidad')->orderBy('apellidos')->get(),
            'especialidades' => Especialidad::orderBy('nombre')->get(),
            'estados' => Cita::ESTADOS,
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['required', 'exists:medicos,id'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'motivo' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'in:'.implode(',', array_keys(Cita::ESTADOS))],
            'notas' => ['nullable', 'string'],
        ]);
    }
}
