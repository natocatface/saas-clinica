@extends('layouts.app')
@section('title', 'Reportes')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'chart', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Reportes</h1>
                <p class="text-sm text-slate-500">Indicadores del {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</p>
            </div>
        </div>
        <form method="GET" class="flex items-end gap-2">
            <div>
                <label class="block text-xs text-slate-400 mb-1">Desde</label>
                <input type="date" name="desde" value="{{ $desde }}" class="px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">Hasta</label>
                <input type="date" name="hasta" value="{{ $hasta }}" class="px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
            </div>
            <button class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">Aplicar</button>
            <a href="{{ route('reportes.export', ['desde' => $desde, 'hasta' => $hasta]) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-brand-300 hover:text-brand-600 transition">Exportar CSV</a>
        </form>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">Ingresos cobrados</p>
            <p class="text-3xl font-extrabold mt-1">{{ money($kpis['ingresos'], 0) }}</p>
        </div>
        <div class="rounded-3xl bg-gradient-to-br from-amber-500 to-orange-500 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">Por cobrar</p>
            <p class="text-3xl font-extrabold mt-1">{{ money($kpis['pendiente'], 0) }}</p>
        </div>
        <div class="rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">Citas en el periodo</p>
            <p class="text-3xl font-extrabold mt-1">{{ $kpis['citas'] }}</p>
        </div>
        <div class="rounded-3xl bg-gradient-to-br from-sky-500 to-sky-600 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">Pacientes nuevos</p>
            <p class="text-3xl font-extrabold mt-1">{{ $kpis['pacientesNuevos'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Ingresos por mes</h2>
            <p class="text-xs text-slate-400 mb-4">Facturas pagadas</p>
            <div class="h-72"><canvas id="chartIngresos"></canvas></div>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Citas por estado</h2>
            <p class="text-xs text-slate-400 mb-4">Distribucion</p>
            <div class="h-72"><canvas id="chartEstado"></canvas></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Citas por especialidad</h2>
            <p class="text-xs text-slate-400 mb-4">Demanda por area</p>
            <div class="h-72"><canvas id="chartEsp"></canvas></div>
        </div>

        <div class="rounded-3xl bg-white shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100"><h2 class="font-bold text-slate-800">Productividad por medico</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                            <th class="px-6 py-3 font-semibold">Medico</th>
                            <th class="px-6 py-3 font-semibold">Especialidad</th>
                            <th class="px-6 py-3 font-semibold text-right">Citas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($medicos as $m)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3 font-medium text-slate-700">Dr(a). {{ $m->nombre_completo }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $m->especialidad?->nombre ?? '—' }}</td>
                                <td class="px-6 py-3 text-right"><span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600">{{ $m->citas_count }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-10 text-center text-slate-400">Sin datos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const ctxI = document.getElementById('chartIngresos');
    const g = ctxI.getContext('2d').createLinearGradient(0,0,0,290);
    g.addColorStop(0,'rgba(52,211,153,0.35)'); g.addColorStop(1,'rgba(52,211,153,0)');
    new Chart(ctxI, {
        type: 'bar',
        data: { labels: @json($ingresosPorMes['labels']), datasets: [{ label: '{{ moneda() }}', data: @json($ingresosPorMes['data']), backgroundColor: '#34d399', borderRadius: 8, maxBarThickness: 38 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' } }, x: { grid: { display: false }, ticks: { color: '#94a3b8' } } } }
    });

    new Chart(document.getElementById('chartEstado'), {
        type: 'doughnut',
        data: { labels: @json($citasEstado['labels']), datasets: [{ data: @json($citasEstado['data']),
            backgroundColor: ['#38bdf8','#34d399','#fbbf24','#7c44ff','#fb7185'], borderWidth: 0, hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%',
            plugins: { legend: { position: 'bottom', labels: { color: '#64748b', usePointStyle: true, padding: 12, font: { size: 11 } } } } }
    });

    new Chart(document.getElementById('chartEsp'), {
        type: 'bar',
        data: { labels: @json($citasEsp['labels']), datasets: [{ data: @json($citasEsp['data']), backgroundColor: '#7c44ff', borderRadius: 8, maxBarThickness: 38 }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8' } }, y: { grid: { display: false }, ticks: { color: '#64748b' } } } }
    });
</script>
@endpush
