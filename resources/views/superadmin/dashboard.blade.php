@extends('layouts.superadmin')
@section('title', 'Dashboard global')

@php
    $estadoColor = [
        'prueba' => 'bg-sky-100 text-sky-700',
        'activa' => 'bg-emerald-100 text-emerald-700',
        'suspendida' => 'bg-amber-100 text-amber-700',
        'cancelada' => 'bg-rose-100 text-rose-700',
    ];
@endphp

@section('content')

    @include('partials.flash')

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        <div class="rounded-3xl bg-gradient-to-br from-iris-600 to-iris-500 text-white p-5 shadow-glow">
            <p class="text-sm text-white/80">Clinicas totales</p>
            <p class="text-3xl font-extrabold mt-1">{{ $kpis['clinicas'] }}</p>
            <p class="text-xs text-white/70 mt-1">{{ $kpis['activas'] }} activas · {{ $kpis['prueba'] }} en prueba</p>
        </div>
        <div class="rounded-3xl bg-gradient-to-br from-acc-600 to-acc-500 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">MRR estimado</p>
            <p class="text-3xl font-extrabold mt-1">Bs {{ number_format($kpis['mrr'], 0) }}</p>
            <p class="text-xs text-white/70 mt-1">Ingreso recurrente mensual</p>
        </div>
        <div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">Facturado (global)</p>
            <p class="text-3xl font-extrabold mt-1">Bs {{ number_format($kpis['facturado'], 0) }}</p>
            <p class="text-xs text-white/70 mt-1">Todas las clinicas</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-card">
            <p class="text-sm text-slate-400">Usuarios · Pacientes</p>
            <p class="text-3xl font-extrabold mt-1 text-slate-800">{{ $kpis['usuarios'] }} · {{ $kpis['pacientes'] }}</p>
            <p class="text-xs text-slate-400 mt-1">En toda la plataforma</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Nuevas clinicas por mes</h2>
            <p class="text-xs text-slate-400 mb-4">Crecimiento de la plataforma</p>
            <div class="h-72"><canvas id="chartClinicas"></canvas></div>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-card">
            <h2 class="font-bold text-slate-800 mb-1">Clinicas por plan</h2>
            <p class="text-xs text-slate-400 mb-4">Distribucion</p>
            <div class="h-72"><canvas id="chartPlan"></canvas></div>
        </div>
    </div>

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Clinicas recientes</h2>
            <a href="{{ route('superadmin.clinicas.index') }}" class="text-sm font-medium text-iris-600 hover:underline">Ver todas</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Clinica</th>
                        <th class="px-6 py-3 font-semibold">Plan</th>
                        <th class="px-6 py-3 font-semibold">Usuarios</th>
                        <th class="px-6 py-3 font-semibold">Estado</th>
                        <th class="px-6 py-3 font-semibold">Alta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clinicasRecientes as $c)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg grid place-items-center text-white text-xs font-bold" style="background: {{ $c->color }}">{{ strtoupper(substr($c->nombre,0,2)) }}</span>
                                    <span class="font-semibold text-slate-700">{{ $c->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $c->plan?->nombre ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $c->usuarios_count }}</td>
                            <td class="px-6 py-3.5"><span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$c->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $c->estadoLabel() }}</span></td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $c->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">Aun no hay clinicas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const ctxC = document.getElementById('chartClinicas');
    const g = ctxC.getContext('2d').createLinearGradient(0,0,0,290);
    g.addColorStop(0,'rgba(99,102,241,0.35)'); g.addColorStop(1,'rgba(99,102,241,0)');
    new Chart(ctxC, {
        type: 'line',
        data: { labels: @json($nuevasClinicas['labels']), datasets: [{ data: @json($nuevasClinicas['data']),
            borderColor: '#6366f1', borderWidth: 3, backgroundColor: g, fill: true, tension: .4, pointRadius: 0, pointHoverRadius: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', precision: 0 } }, x: { grid: { display: false }, ticks: { color: '#94a3b8' } } } }
    });

    new Chart(document.getElementById('chartPlan'), {
        type: 'doughnut',
        data: { labels: @json($clinicasPorPlan['labels']), datasets: [{ data: @json($clinicasPorPlan['data']),
            backgroundColor: ['#6366f1','#22d3ee','#34d399','#fbbf24','#fb7185'], borderWidth: 0, hoverOffset: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '62%',
            plugins: { legend: { position: 'bottom', labels: { color: '#64748b', usePointStyle: true, padding: 12, font: { size: 11 } } } } }
    });
</script>
@endpush
