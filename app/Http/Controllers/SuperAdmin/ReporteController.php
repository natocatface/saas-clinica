<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Clinica;
use App\Models\Paciente;
use App\Models\Suscripcion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        $totalClinicas = Clinica::count();
        $activas = Clinica::where('estado', 'activa')->count();
        $canceladas = Clinica::whereIn('estado', ['cancelada', 'suspendida'])->count();
        $mrr = (float) Suscripcion::where('estado', 'activa')->where('ciclo', 'mensual')->sum('monto');

        $kpis = [
            'mrr' => $mrr,
            'arr' => $mrr * 12,
            'arpu' => $activas > 0 ? $mrr / $activas : 0,
            'churn' => $totalClinicas > 0 ? round($canceladas / $totalClinicas * 100, 1) : 0,
        ];

        // Ingresos (MRR) por plan
        $porPlan = Suscripcion::where('suscripciones.estado', 'activa')
            ->leftJoin('planes', 'suscripciones.plan_id', '=', 'planes.id')
            ->selectRaw('planes.nombre as plan, sum(suscripciones.monto) as total')
            ->groupBy('planes.nombre')->get();
        $ingresosPlan = [
            'labels' => $porPlan->map(fn ($r) => $r->plan ?? 'Sin plan')->toArray() ?: ['Sin datos'],
            'data' => $porPlan->pluck('total')->map(fn ($v) => (float) $v)->toArray() ?: [0],
        ];

        // Distribucion por estado
        $estados = Clinica::selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado');
        $estadoDist = [
            'labels' => collect(Clinica::ESTADOS)->values()->toArray(),
            'data' => collect(Clinica::ESTADOS)->keys()->map(fn ($k) => (int) ($estados[$k] ?? 0))->toArray(),
        ];

        // Crecimiento: nuevas clinicas por mes (6 meses)
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $labels = [];
        $nuevas = [];
        for ($i = 5; $i >= 0; $i--) {
            $ref = $hoy->copy()->subMonths($i);
            $labels[] = $meses[$ref->month - 1];
            $nuevas[] = Clinica::whereMonth('created_at', $ref->month)->whereYear('created_at', $ref->year)->count();
        }
        $crecimiento = ['labels' => $labels, 'data' => $nuevas];

        // Ranking de clinicas por actividad
        $pac = Paciente::selectRaw('clinica_id, count(*) c')->groupBy('clinica_id')->pluck('c', 'clinica_id');
        $cit = Cita::selectRaw('clinica_id, count(*) c')->groupBy('clinica_id')->pluck('c', 'clinica_id');
        $ranking = Clinica::with('plan')->withCount('usuarios')->get()
            ->map(function ($c) use ($pac, $cit) {
                $c->pacientes_total = (int) ($pac[$c->id] ?? 0);
                $c->citas_total = (int) ($cit[$c->id] ?? 0);
                return $c;
            })
            ->sortByDesc(fn ($c) => $c->pacientes_total + $c->citas_total)
            ->take(10)->values();

        return view('superadmin.reportes', compact('kpis', 'ingresosPlan', 'estadoDist', 'crecimiento', 'ranking'));
    }
}
