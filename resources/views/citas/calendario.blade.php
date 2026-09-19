@extends('layouts.app')
@section('title', 'Agenda')

@php
    use Carbon\Carbon;
    $estadoColor = [
        'programada' => 'bg-sky-100 text-sky-700',
        'confirmada' => 'bg-emerald-100 text-emerald-700',
        'en_espera'  => 'bg-amber-100 text-amber-700',
        'atendida'   => 'bg-brand-100 text-brand-700',
        'cancelada'  => 'bg-rose-100 text-rose-700 line-through',
    ];
    $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $inicioGrid = $inicio->copy()->startOfWeek(Carbon::MONDAY);
    $finGrid = $fin->copy()->endOfWeek(Carbon::SUNDAY);
    $hoy = Carbon::today()->toDateString();
    $prev = $ref->copy()->subMonth()->format('Y-m');
    $next = $ref->copy()->addMonth()->format('Y-m');
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'calendar', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">{{ $meses[$ref->month] }} {{ $ref->year }}</h1>
                <p class="text-sm text-slate-500">Agenda mensual de citas</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('citas.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-brand-300 hover:text-brand-600 transition">Vista lista</a>
            <a href="{{ route('citas.calendario', ['mes' => $prev]) }}" class="w-10 h-10 grid place-items-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:border-brand-300 transition">‹</a>
            <a href="{{ route('citas.calendario') }}" class="px-3 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-brand-300 transition">Hoy</a>
            <a href="{{ route('citas.calendario', ['mes' => $next]) }}" class="w-10 h-10 grid place-items-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:border-brand-300 transition">›</a>
        </div>
    </div>

    @include('partials.flash')

    <div class="rounded-3xl bg-white shadow-card p-4 sm:p-6">
        <div class="grid grid-cols-7 gap-2 mb-2 text-center text-xs font-semibold uppercase tracking-wide text-slate-400">
            @foreach (['Lun','Mar','Mie','Jue','Vie','Sab','Dom'] as $d)<div>{{ $d }}</div>@endforeach
        </div>

        <div class="grid grid-cols-7 gap-2">
            @php $cursor = $inicioGrid->copy(); @endphp
            @while ($cursor <= $finGrid)
                @php
                    $dia = $cursor->toDateString();
                    $delMes = $cursor->month === $ref->month;
                    $esHoy = $dia === $hoy;
                    $citasDia = $citasPorDia[$dia] ?? collect();
                @endphp
                <div class="min-h-[110px] rounded-2xl border p-2 {{ $delMes ? 'bg-white border-slate-100' : 'bg-slate-50/60 border-transparent' }} {{ $esHoy ? 'ring-2 ring-brand-300' : '' }}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-semibold {{ $delMes ? 'text-slate-600' : 'text-slate-300' }} {{ $esHoy ? 'w-6 h-6 grid place-items-center rounded-full bg-brand-600 text-white' : '' }}">{{ $cursor->day }}</span>
                        @if ($delMes)
                            <a href="{{ route('citas.create', ['fecha' => $dia]) }}" class="text-slate-300 hover:text-brand-600 transition" title="Nueva cita">+</a>
                        @endif
                    </div>
                    <div class="space-y-1">
                        @foreach ($citasDia->take(4) as $c)
                            <a href="{{ route('citas.edit', $c) }}" class="block text-[11px] px-1.5 py-1 rounded-lg truncate {{ $estadoColor[$c->estado] ?? 'bg-slate-100 text-slate-600' }}" title="{{ $c->paciente?->nombre_completo }} · {{ $c->estadoLabel() }}">
                                {{ substr($c->hora,0,5) }} {{ $c->paciente?->apellidos ?? '' }}
                            </a>
                        @endforeach
                        @if ($citasDia->count() > 4)
                            <p class="text-[10px] text-slate-400 px-1">+{{ $citasDia->count() - 4 }} mas</p>
                        @endif
                    </div>
                </div>
                @php $cursor->addDay(); @endphp
            @endwhile
        </div>
    </div>

    <!-- Leyenda -->
    <div class="flex flex-wrap items-center gap-4 mt-4 text-xs text-slate-500">
        @foreach ($estados as $k => $v)
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full {{ $estadoColor[$k] ?? 'bg-slate-200' }}"></span>{{ $v }}</span>
        @endforeach
    </div>

@endsection
