@extends('layouts.app')
@section('title', 'Mi agenda')

@php
    $tones = [
        'violet'  => 'from-brand-500 to-brand-700 text-white',
        'sky'     => 'from-sky-500 to-sky-600 text-white',
        'emerald' => 'from-emerald-500 to-emerald-600 text-white',
        'amber'   => 'from-amber-500 to-orange-500 text-white',
    ];
    $estadoColor = [
        'programada' => 'bg-sky-100 text-sky-700',
        'confirmada' => 'bg-emerald-100 text-emerald-700',
        'en_espera'  => 'bg-amber-100 text-amber-700',
        'atendida'   => 'bg-brand-100 text-brand-700',
        'cancelada'  => 'bg-rose-100 text-rose-700',
    ];
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Mi agenda</h1>
            <p class="text-sm text-slate-500">
                {{ $medico ? 'Dr(a). '.$medico->nombre_completo : auth()->user()->name }} · {{ now()->translatedFormat('d \d\e F, Y') }}
            </p>
        </div>
        <a href="{{ route('citas.calendario') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
            @include('partials.icon', ['name' => 'calendar', 'class' => 'w-4 h-4'])
            Ver calendario
        </a>
    </div>

    @include('partials.flash')

    @unless ($medico)
        <div class="mb-6 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-700">
            Tu cuenta no esta vinculada a una ficha de medico (por correo). Se muestran las citas de la clinica. Pide al administrador que use el mismo correo en tu ficha de medico.
        </div>
    @endunless

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
        @foreach ($kpis as $kpi)
            <div class="rounded-3xl bg-gradient-to-br {{ $tones[$kpi['tone']] }} p-5 shadow-card relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full"></div>
                <div class="relative w-11 h-11 rounded-2xl bg-white/20 grid place-items-center mb-4">
                    @include('partials.icon', ['name' => $kpi['icon'], 'class' => 'w-6 h-6'])
                </div>
                <p class="relative text-3xl font-extrabold">{{ $kpi['value'] }}</p>
                <p class="relative text-sm text-white/80">{{ $kpi['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <!-- Agenda de hoy -->
        <div class="rounded-3xl bg-white shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-800">Agenda de hoy</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                            <th class="px-6 py-3 font-semibold">Hora</th>
                            <th class="px-6 py-3 font-semibold">Paciente</th>
                            <th class="px-6 py-3 font-semibold">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($agendaHoy as $c)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 font-semibold text-slate-700">{{ substr($c->hora,0,5) }}</td>
                                <td class="px-6 py-3.5">
                                    <p class="font-medium text-slate-700">{{ $c->paciente?->nombre_completo ?? '—' }}</p>
                                    <p class="text-xs text-slate-400">{{ $c->especialidad?->nombre }}</p>
                                </td>
                                <td class="px-6 py-3.5"><span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$c->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $c->estadoLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-12 text-center text-slate-400">No tienes citas para hoy.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Proximas citas -->
        <div class="rounded-3xl bg-white shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-800">Proximas citas</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                            <th class="px-6 py-3 font-semibold">Fecha</th>
                            <th class="px-6 py-3 font-semibold">Paciente</th>
                            <th class="px-6 py-3 font-semibold">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($proximas as $c)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-700">{{ $c->fecha->format('d/m/Y') }} · {{ substr($c->hora,0,5) }}</td>
                                <td class="px-6 py-3.5 text-slate-600">{{ $c->paciente?->nombre_completo ?? '—' }}</td>
                                <td class="px-6 py-3.5"><span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$c->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $c->estadoLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-12 text-center text-slate-400">Sin proximas citas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
