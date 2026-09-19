<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Factura;
use App\Models\Paciente;
use App\Models\Suscripcion;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        $kpis = [
            'clinicas' => Clinica::count(),
            'activas' => Clinica::where('estado', 'activa')->count(),
            'prueba' => Clinica::where('estado', 'prueba')->count(),
            'suspendidas' => Clinica::where('estado', 'suspendida')->count(),
            'usuarios' => User::whereNotNull('clinica_id')->count(),
            'pacientes' => Paciente::count(),
            'mrr' => Suscripcion::where('estado', 'activa')->where('ciclo', 'mensual')->sum('monto')
                   + Suscripcion::where('estado', 'activa')->where('ciclo', 'anual')->sum('monto') / 12,
            'facturado' => Factura::where('estado', 'pagada')->sum('total'),
        ];

        // Clinicas por plan
        $porPlan = Clinica::selectRaw('planes.nombre as plan, count(*) as total')
            ->leftJoin('planes', 'clinicas.plan_id', '=', 'planes.id')
            ->groupBy('planes.nombre')->get();
        $clinicasPorPlan = [
            'labels' => $porPlan->map(fn ($r) => $r->plan ?? 'Sin plan')->toArray() ?: ['Sin datos'],
            'data' => $porPlan->pluck('total')->toArray() ?: [0],
        ];

        // Nuevas clinicas por mes (6 meses)
        $meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $ref = $hoy->copy()->subMonths($i);
            $labels[] = $meses[$ref->month - 1];
            $data[] = Clinica::whereMonth('created_at', $ref->month)->whereYear('created_at', $ref->year)->count();
        }
        $nuevasClinicas = ['labels' => $labels, 'data' => $data];

        $clinicasRecientes = Clinica::with('plan')->withCount('usuarios')->latest()->take(6)->get();

        return view('superadmin.dashboard', compact('kpis', 'clinicasPorPlan', 'nuevasClinicas', 'clinicasRecientes'));
    }
}
