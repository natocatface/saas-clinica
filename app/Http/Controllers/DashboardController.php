<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Factura;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Panel especifico para el medico: su agenda del dia
        if (auth()->user()?->role === 'medico') {
            return $this->panelMedico();
        }

        $hoy = Carbon::today();

        $citasHoy = Cita::whereDate('fecha', $hoy)->count();
        $pacientesActivos = Paciente::where('activo', true)->count();

        // Ingresos del mes: tarifa de la especialidad de citas atendidas
        $ingresosMes = Cita::where('estado', 'atendida')
            ->whereMonth('fecha', $hoy->month)
            ->whereYear('fecha', $hoy->year)
            ->join('especialidades', 'citas.especialidad_id', '=', 'especialidades.id')
            ->sum('especialidades.tarifa');

        // Ocupacion: % de citas no canceladas sobre el total (del mes)
        $totalMes = Cita::whereMonth('fecha', $hoy->month)->whereYear('fecha', $hoy->year)->count();
        $efectivas = Cita::whereMonth('fecha', $hoy->month)->whereYear('fecha', $hoy->year)
            ->whereIn('estado', ['confirmada', 'atendida'])->count();
        $ocupacion = $totalMes > 0 ? round($efectivas / $totalMes * 100) : 0;

        $kpis = [
            ['label' => 'Citas de hoy', 'value' => $citasHoy, 'delta' => 'hoy', 'up' => true, 'icon' => 'calendar', 'tone' => 'violet'],
            ['label' => 'Pacientes activos', 'value' => number_format($pacientesActivos), 'delta' => 'total', 'up' => true, 'icon' => 'users', 'tone' => 'sky'],
            ['label' => 'Ingresos del mes', 'value' => money($ingresosMes, 0), 'delta' => 'atendidas', 'up' => true, 'icon' => 'cash', 'tone' => 'emerald'],
            ['label' => 'Ocupacion', 'value' => $ocupacion.'%', 'delta' => 'del mes', 'up' => $ocupacion >= 50, 'icon' => 'pulse', 'tone' => 'amber'],
        ];

        // Citas por mes (ultimos 8 meses)
        $labels = [];
        $data = [];
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        for ($i = 7; $i >= 0; $i--) {
            $ref = $hoy->copy()->subMonths($i);
            $labels[] = $meses[$ref->month - 1];
            $data[] = Cita::whereMonth('fecha', $ref->month)->whereYear('fecha', $ref->year)->count();
        }
        $citasPorMes = ['labels' => $labels, 'data' => $data];

        // Distribucion por especialidad
        $espRows = Especialidad::withCount('citas')->orderByDesc('citas_count')->take(5)->get();
        $especialidades = [
            'labels' => $espRows->pluck('nombre')->toArray() ?: ['Sin datos'],
            'data' => $espRows->pluck('citas_count')->toArray() ?: [1],
        ];

        // Ingresos por mes (facturas pagadas, ultimos 6 meses)
        $ingLabels = [];
        $ingData = [];
        for ($i = 5; $i >= 0; $i--) {
            $ref = $hoy->copy()->subMonths($i);
            $ingLabels[] = $meses[$ref->month - 1];
            $ingData[] = (float) Factura::where('estado', 'pagada')
                ->whereMonth('fecha', $ref->month)->whereYear('fecha', $ref->year)->sum('total');
        }
        $ingresosPorMes = ['labels' => $ingLabels, 'data' => $ingData];

        // Citas por estado
        $estadoRows = Cita::select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado');
        $citasPorEstado = [
            'labels' => collect(Cita::ESTADOS)->values()->toArray(),
            'data' => collect(Cita::ESTADOS)->keys()->map(fn ($k) => (int) ($estadoRows[$k] ?? 0))->toArray(),
        ];

        // Proximas citas de hoy
        $proximasCitas = Cita::with(['paciente', 'medico', 'especialidad'])
            ->whereDate('fecha', $hoy)
            ->orderBy('hora')
            ->take(6)
            ->get()
            ->map(fn ($c) => [
                'hora' => substr($c->hora, 0, 5),
                'paciente' => $c->paciente?->nombre_completo ?? '—',
                'medico' => 'Dr(a). '.($c->medico?->apellidos ?? '—'),
                'especialidad' => $c->especialidad?->nombre ?? '—',
                'estado' => $c->estadoLabel(),
            ])
            ->toArray();

        return view('dashboard.index', compact('kpis', 'citasPorMes', 'especialidades', 'proximasCitas', 'ingresosPorMes', 'citasPorEstado'));
    }

    /** Panel del medico: su agenda y estadisticas personales. */
    private function panelMedico()
    {
        $hoy = Carbon::today();
        $user = auth()->user();

        // Se intenta vincular la cuenta con su ficha de medico por correo
        $medico = Medico::where('email', $user->email)->first();
        $medicoId = $medico?->id;

        $base = fn () => Cita::query()->when($medicoId, fn ($q) => $q->where('medico_id', $medicoId));

        $kpis = [
            ['label' => 'Mis citas de hoy', 'value' => (clone $base())->whereDate('fecha', $hoy)->count(), 'icon' => 'calendar', 'tone' => 'violet'],
            ['label' => 'Atendidas hoy', 'value' => (clone $base())->whereDate('fecha', $hoy)->where('estado', 'atendida')->count(), 'icon' => 'pulse', 'tone' => 'emerald'],
            ['label' => 'Pendientes hoy', 'value' => (clone $base())->whereDate('fecha', $hoy)->whereIn('estado', ['programada', 'confirmada', 'en_espera'])->count(), 'icon' => 'clipboard', 'tone' => 'amber'],
            ['label' => 'Citas este mes', 'value' => (clone $base())->whereMonth('fecha', $hoy->month)->whereYear('fecha', $hoy->year)->count(), 'icon' => 'chart', 'tone' => 'sky'],
        ];

        $agendaHoy = (clone $base())
            ->with(['paciente', 'especialidad'])
            ->whereDate('fecha', $hoy)
            ->orderBy('hora')
            ->get();

        $proximas = (clone $base())
            ->with(['paciente', 'especialidad'])
            ->whereDate('fecha', '>', $hoy)
            ->where('estado', '!=', 'cancelada')
            ->orderBy('fecha')->orderBy('hora')
            ->take(8)->get();

        return view('dashboard.medico', [
            'medico' => $medico,
            'kpis' => $kpis,
            'agendaHoy' => $agendaHoy,
            'proximas' => $proximas,
        ]);
    }
}
