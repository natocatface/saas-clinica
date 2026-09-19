@extends('layouts.superadmin')
@section('title', 'Reportes globales')

@php
    $estadoColor = ['prueba'=>'bg-sky-100 text-sky-700','activa'=>'bg-emerald-100 text-emerald-700','suspendida'=>'bg-amber-100 text-amber-700','cancelada'=>'bg-rose-100 text-rose-700'];
@endphp

@section('content')

    @include('partials.flash')

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="rounded-3xl bg-gradient-to-br from-acc-600 to-acc-500 text-white p-5 shadow-card"><p class="text-sm text-white/80">MRR</p><p class="text-3xl font-extrabold mt-1">Bs {{ number_format($kpis['mrr'], 0) }}</p></div>
        <div class="rounded-3xl bg-gradient-to-br from-iris-600 to-iris-500 text-white p-5 shadow-glow"><p class="text-sm text-white/80">ARR (anual)</p><p class="text-3xl font-extrabold mt-1">Bs {{ number_format($kpis['arr'], 0) }}</p></div>
        <div class="rounded-3xl bg-white p-5 shadow-card"><p class="text-sm text-slate-400">ARPU</p><p class="text-3xl font-extrabold mt-1 text-slate-800">Bs {{ number_format($kpis['arpu'], 0) }}</p></div>
        <div class="rounded-3xl bg-white p-5 shadow-card"><p class="text-sm text-slate-400">Churn</p><p class="text-3xl font-extrabold mt-1 text-slate-800">{{ $kpis['churn'] }}%</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Crecimiento de clinicas</h2>
            <p class="text-xs text-slate-400 mb-4">Nuevas altas por mes</p>
            <div class="h-72"><canvas id="chartCrec"></canvas></div>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Clinicas por estado</h2>
            <p class="text-xs text-slate-400 mb-4">Distribucion</p>
            <div class="h-72"><canvas id="chartEstado"></canvas></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Ingresos (MRR) por plan</h2>
            <p class="text-xs text-slate-400 mb-4">Suscripciones activas</p>
            <div class="h-72"><canvas id="chartPlan"></canvas></div>
        </div>

        <div class="rounded-3xl bg-white shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100"><h2 class="font-bold text-slate-800">Ranking de clinicas por actividad</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Clinica</th><th class="px-6 py-3 font-semibold">Plan</th><th class="px-6 py-3 font-semibold text-right">Pacientes</th><th class="px-6 py-3 font-semibold text-right">Citas</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($ranking as $c)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg grid place-items-center text-white text-[10px] font-bold" style="background: {{ $c->color }}">{{ strtoupper(substr($c->nombre,0,2)) }}</span>
                                        <span class="font-medium text-slate-700">{{ $c->nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-slate-500">{{ $c->plan?->nombre ?? '—' }}</td>
                                <td class="px-6 py-3 text-right font-semibold text-slate-700">{{ $c->pacientes_total }}</td>
                                <td class="px-6 py-3 text-right text-slate-500">{{ $c->citas_total }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400">Sin datos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const gc = document.getElementById('chartCrec').getContext('2d').createLinearGradient(0,0,0,290);
    gc.addColorStop(0,'rgba(99,102,241,0.35)'); gc.addColorStop(1,'rgba(99,102,241,0)');
    new Chart(document.getElementById('chartCrec'), {
        type: 'line',
        data: { labels: @json($crecimiento['labels']), datasets: [{ data: @json($crecimiento['data']), borderColor:'#6366f1', borderWidth:3, backgroundColor:gc, fill:true, tension:.4, pointRadius:0, pointHoverRadius:6 }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true, grid:{color:'#f1f5f9'}, ticks:{color:'#94a3b8', precision:0}}, x:{grid:{display:false}, ticks:{color:'#94a3b8'}} } }
    });
    new Chart(document.getElementById('chartEstado'), {
        type:'doughnut',
        data:{ labels:@json($estadoDist['labels']), datasets:[{ data:@json($estadoDist['data']), backgroundColor:['#38bdf8','#34d399','#fbbf24','#fb7185'], borderWidth:0, hoverOffset:6 }] },
        options:{ responsive:true, maintainAspectRatio:false, cutout:'62%', plugins:{legend:{position:'bottom', labels:{color:'#64748b', usePointStyle:true, padding:12, font:{size:11}}}} }
    });
    new Chart(document.getElementById('chartPlan'), {
        type:'bar',
        data:{ labels:@json($ingresosPlan['labels']), datasets:[{ data:@json($ingresosPlan['data']), backgroundColor:'#06b6d4', borderRadius:8, maxBarThickness:48 }] },
        options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true, grid:{color:'#f1f5f9'}, ticks:{color:'#94a3b8'}}, x:{grid:{display:false}, ticks:{color:'#64748b'}} } }
    });
</script>
@endpush
