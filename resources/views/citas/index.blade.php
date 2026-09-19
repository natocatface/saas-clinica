@extends('layouts.app')
@section('title', 'Citas y Agenda')

@php
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
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'calendar', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Citas y Agenda</h1>
                <p class="text-sm text-slate-500">{{ $citas->total() }} cita(s) en total</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('citas.calendario') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-brand-300 hover:text-brand-600 transition shadow-sm flex items-center gap-2">
                @include('partials.icon', ['name' => 'calendar', 'class' => 'w-4 h-4'])
                Calendario
            </a>
            <a href="{{ route('citas.export', ['q' => $q, 'estado' => $estado]) }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-brand-300 hover:text-brand-600 transition shadow-sm">
                Exportar CSV
            </a>
            <a href="{{ route('citas.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
                @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
                Nueva cita
            </a>
        </div>
    </div>

    @include('partials.flash')

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])</span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar paciente..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-brand-300 focus:ring-2 focus:ring-brand-100 outline-none transition">
                </div>
                <select name="estado" onchange="this.form.submit()" class="px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-brand-300 outline-none">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $k => $v)
                        <option value="{{ $k }}" {{ $estado === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2.5 rounded-full bg-brand-50 text-brand-600 text-sm font-medium hover:bg-brand-100 transition">Filtrar</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Fecha / Hora</th>
                        <th class="px-6 py-3 font-semibold">Paciente</th>
                        <th class="px-6 py-3 font-semibold">Medico</th>
                        <th class="px-6 py-3 font-semibold">Especialidad</th>
                        <th class="px-6 py-3 font-semibold">Estado</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($citas as $cita)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5">
                                <p class="font-semibold text-slate-700">{{ $cita->fecha->format('d/m/Y') }}</p>
                                <p class="text-xs text-slate-400">{{ substr($cita->hora,0,5) }}</p>
                            </td>
                            <td class="px-6 py-3.5 font-medium text-slate-700">{{ $cita->paciente?->nombre_completo ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500">Dr(a). {{ $cita->medico?->nombre_completo ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $cita->especialidad?->nombre ?? '—' }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$cita->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $cita->estadoLabel() }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($cita->estado !== 'cancelada')
                                        @if ($cita->recordatorio_enviado)
                                            <span class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-emerald-600 bg-emerald-50" title="Recordado {{ $cita->recordatorio_at?->format('d/m H:i') }}">✓ Recordado</span>
                                        @else
                                            <form method="POST" action="{{ route('citas.recordar', $cita) }}">
                                                @csrf @method('PATCH')
                                                <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-amber-600 bg-amber-50 hover:bg-amber-100 transition">Recordar</button>
                                            </form>
                                        @endif
                                    @endif
                                    <a href="{{ route('citas.edit', $cita) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('citas.destroy', $cita) }}" onsubmit="return confirm('Eliminar esta cita?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">No hay citas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $citas->links() }}</div>
    </div>

@endsection
