<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Factura;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->date('desde') ?: Carbon::today()->startOfMonth()->subMonths(5);
        $hasta = $request->date('hasta') ?: Carbon::today()->endOfDay();
        $desde = Carbon::parse($desde)->startOfDay();
        $hasta = Carbon::parse($hasta)->endOfDay();

        // KPIs
        $kpis = [
            'ingresos' => Factura::where('estado', 'pagada')->whereBetween('fecha', [$desde, $hasta])->sum('total'),
            'pendiente' => Factura::where('estado', 'pendiente')->whereBetween('fecha', [$desde, $hasta])->sum('total'),
            'citas' => Cita::whereBetween('fecha', [$desde, $hasta])->count(),
            'pacientesNuevos' => Paciente::whereBetween('created_at', [$desde, $hasta])->count(),
        ];

        // Ingresos por mes (rango)
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $ingresosLabels = [];
        $ingresosData = [];
        $cursor = $desde->copy()->startOfMonth();
        while ($cursor <= $hasta) {
            $ingresosLabels[] = $meses[$cursor->month - 1].' '.$cursor->format('y');
            $ingresosData[] = (float) Factura::where('estado', 'pagada')
                ->whereMonth('fecha', $cursor->month)->whereYear('fecha', $cursor->year)->sum('total');
            $cursor->addMonth();
        }

        // Citas por estado
        $estadoRows = Cita::whereBetween('fecha', [$desde, $hasta])
            ->select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado');
        $citasEstado = [
            'labels' => collect(Cita::ESTADOS)->map(fn ($v) => $v)->values()->toArray(),
            'data' => collect(Cita::ESTADOS)->keys()->map(fn ($k) => (int) ($estadoRows[$k] ?? 0))->toArray(),
        ];

        // Citas por especialidad
        $espRows = Cita::whereBetween('fecha', [$desde, $hasta])
            ->join('especialidades', 'citas.especialidad_id', '=', 'especialidades.id')
            ->select('especialidades.nombre', DB::raw('count(*) as total'))
            ->groupBy('especialidades.nombre')->orderByDesc('total')->get();
        $citasEsp = [
            'labels' => $espRows->pluck('nombre')->toArray() ?: ['Sin datos'],
            'data' => $espRows->pluck('total')->toArray() ?: [0],
        ];

        // Productividad por medico
        $medicos = Medico::withCount(['citas' => fn ($q) => $q->whereBetween('fecha', [$desde, $hasta])])
            ->orderByDesc('citas_count')->take(10)->get();

        return view('reportes.index', [
            'desde' => $desde->format('Y-m-d'),
            'hasta' => $hasta->format('Y-m-d'),
            'kpis' => $kpis,
            'ingresosPorMes' => ['labels' => $ingresosLabels, 'data' => $ingresosData],
            'citasEstado' => $citasEstado,
            'citasEsp' => $citasEsp,
            'medicos' => $medicos,
        ]);
    }

    /** Exporta la productividad por medico del rango a CSV. */
    public function export(Request $request)
    {
        $desde = Carbon::parse($request->date('desde') ?: Carbon::today()->startOfMonth()->subMonths(5))->startOfDay();
        $hasta = Carbon::parse($request->date('hasta') ?: Carbon::today())->endOfDay();

        $medicos = Medico::with('especialidad')
            ->withCount(['citas' => fn ($q) => $q->whereBetween('fecha', [$desde, $hasta])])
            ->orderByDesc('citas_count')->get();

        $filas = $medicos->map(fn ($m) => [
            $m->nombre_completo, $m->especialidad?->nombre, $m->citas_count,
        ]);

        return csv_descarga(
            'reporte_medicos_'.now()->format('Ymd').'.csv',
            ['Medico', 'Especialidad', 'Citas en el periodo'],
            $filas
        );
    }
}
