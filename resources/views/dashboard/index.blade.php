@extends('layouts.app')
@section('title', 'Dashboard')

@php
    $tones = [
        'violet'  => 'from-brand-500 to-brand-700 text-white',
        'sky'     => 'from-sky-500 to-sky-600 text-white',
        'emerald' => 'from-emerald-500 to-emerald-600 text-white',
        'amber'   => 'from-amber-500 to-orange-500 text-white',
    ];
    $estadoColor = [
        'Programada' => 'bg-sky-100 text-sky-700',
        'Confirmada' => 'bg-emerald-100 text-emerald-700',
        'En espera'  => 'bg-amber-100 text-amber-700',
        'Atendida'   => 'bg-brand-100 text-brand-700',
        'Cancelada'  => 'bg-rose-100 text-rose-700',
    ];
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Panel general</h1>
            <p class="text-sm text-slate-500">Resumen de la actividad de la clinica · {{ now()->translatedFormat('d \d\e F, Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-brand-300 hover:text-brand-600 transition shadow-sm">
                Exportar
            </button>
            <a href="{{ route('module.show', 'citas') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
                @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
                Nueva cita
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        @foreach ($kpis as $kpi)
            <div class="rounded-3xl bg-gradient-to-br {{ $tones[$kpi['tone']] }} p-5 shadow-card relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="relative flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-white/20 grid place-items-center">
                        @include('partials.icon', ['name' => $kpi['icon'], 'class' => 'w-6 h-6'])
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full bg-white/20">
                        @include('partials.icon', ['name' => $kpi['up'] ? 'arrow-up' : 'arrow-down', 'class' => 'w-3.5 h-3.5'])
                        {{ $kpi['delta'] }}
                    </span>
                </div>
                <p class="relative mt-4 text-3xl font-extrabold">{{ $kpi['value'] }}</p>
                <p class="relative text-sm text-white/80">{{ $kpi['label'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Graficos -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 rounded-3xl bg-white p-6 shadow-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-slate-800">Citas atendidas</h2>
                    <p class="text-xs text-slate-400">Evolucion mensual del numero de citas</p>
                </div>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-3 py-1.5 rounded-full">Este ano</span>
            </div>
            <div class="h-56 sm:h-64 md:h-72"><canvas id="chartCitas"></canvas></div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Por especialidad</h2>
            <p class="text-xs text-slate-400 mb-4">Distribucion de atenciones</p>
            <div class="h-56"><canvas id="chartEsp"></canvas></div>
        </div>
    </div>

    <!-- Nuevos graficos: ingresos por mes + citas por estado -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 rounded-3xl bg-white p-6 shadow-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-bold text-slate-800">Ingresos por mes</h2>
                    <p class="text-xs text-slate-400">Facturas cobradas (ultimos 6 meses)</p>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">{{ moneda() }}</span>
            </div>
            <div class="h-56 sm:h-64"><canvas id="chartIngresos"></canvas></div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Citas por estado</h2>
            <p class="text-xs text-slate-400 mb-4">Distribucion actual</p>
            <div class="h-64"><canvas id="chartEstado"></canvas></div>
        </div>
    </div>

    <!-- Proximas citas -->
    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Proximas citas de hoy</h2>
            <a href="{{ route('module.show', 'citas') }}" class="text-sm font-medium text-brand-600 hover:underline">Ver agenda completa</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Hora</th>
                        <th class="px-6 py-3 font-semibold">Paciente</th>
                        <th class="px-6 py-3 font-semibold">Medico</th>
                        <th class="px-6 py-3 font-semibold">Especialidad</th>
                        <th class="px-6 py-3 font-semibold">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($proximasCitas as $c)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-semibold text-slate-700">{{ $c['hora'] }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 grid place-items-center text-xs font-bold">
                                        {{ strtoupper(substr($c['paciente'],0,1)) }}
                                    </span>
                                    <span class="font-medium text-slate-700">{{ $c['paciente'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $c['medico'] }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $c['especialidad'] }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$c['estado']] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $c['estado'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Citas atendidas (barras verticales con degradado redondeadas)
    const ctxC = document.getElementById('chartCitas');
    const gradC = ctxC.getContext('2d').createLinearGradient(0, 0, 0, 290);
    gradC.addColorStop(0, '#a78bfa');
    gradC.addColorStop(1, '#7c44ff');
    new Chart(ctxC, {
        type: 'bar',
        data: {
            labels: @json($citasPorMes['labels']),
            datasets: [{
                data: @json($citasPorMes['data']),
                label: 'Citas',
                backgroundColor: gradC,
                hoverBackgroundColor: '#6d28d9',
                borderRadius: 12,
                borderSkipped: 'bottom',
                maxBarThickness: 40,
                categoryPercentage: 0.65
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', padding: 12, cornerRadius: 10,
                    titleColor: '#fff', bodyColor: '#e2e8f0', displayColors: false,
                    callbacks: { label: c => Number(c.raw).toLocaleString('es-BO') + ' citas' }
                }
            },
            scales: {
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', precision: 0, padding: 8 } },
                x: { border: { display: false }, grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });

    new Chart(document.getElementById('chartEsp'), {
        type: 'doughnut',
        data: {
            labels: @json($especialidades['labels']),
            datasets: [{
                data: @json($especialidades['data']),
                backgroundColor: ['#7c44ff','#38bdf8','#34d399','#fbbf24','#fb7185'],
                borderWidth: 0, hoverOffset: 6
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { color: '#64748b', usePointStyle: true, padding: 14, font: { size: 11 } } } }
        }
    });

    // Ingresos por mes (area suave con degradado y puntos)
    const ctxI = document.getElementById('chartIngresos');
    const gradI = ctxI.getContext('2d').createLinearGradient(0, 0, 0, 260);
    gradI.addColorStop(0, 'rgba(16,185,129,0.30)');
    gradI.addColorStop(1, 'rgba(16,185,129,0)');
    new Chart(ctxI, {
        type: 'line',
        data: {
            labels: @json($ingresosPorMes['labels']),
            datasets: [{
                data: @json($ingresosPorMes['data']),
                label: 'Ingresos',
                borderColor: '#10b981', borderWidth: 3,
                backgroundColor: gradI, fill: true, tension: 0.4,
                pointBackgroundColor: '#fff', pointBorderColor: '#10b981', pointBorderWidth: 2,
                pointRadius: 4, pointHoverRadius: 7, pointHoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', padding: 12, cornerRadius: 10,
                    titleColor: '#fff', bodyColor: '#e2e8f0', displayColors: false,
                    callbacks: { label: c => '{{ moneda() }} ' + Number(c.raw).toLocaleString('es-BO') }
                }
            },
            scales: {
                y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', padding: 8, callback: v => '{{ moneda() }} ' + Number(v).toLocaleString('es-BO') } },
                x: { border: { display: false }, grid: { display: false }, ticks: { color: '#94a3b8' } }
            }
        }
    });

    // Citas por estado (barras horizontales)
    new Chart(document.getElementById('chartEstado'), {
        type: 'bar',
        data: {
            labels: @json($citasPorEstado['labels']),
            datasets: [{
                data: @json($citasPorEstado['data']),
                backgroundColor: ['#38bdf8','#34d399','#fbbf24','#7c44ff','#fb7185'],
                borderRadius: 8, maxBarThickness: 26
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', precision: 0 } },
                y: { grid: { display: false }, ticks: { color: '#64748b' } }
            }
        }
    });
</script>
@endpush
