@extends('layouts.app')
@section('title', 'Laboratorio')

@php
    $estadoColor = [
        'solicitada' => 'bg-sky-100 text-sky-700',
        'en_proceso' => 'bg-amber-100 text-amber-700',
        'completada' => 'bg-emerald-100 text-emerald-700',
        'entregada' => 'bg-brand-100 text-brand-700',
    ];
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'beaker', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Laboratorio</h1>
                <p class="text-sm text-slate-500">{{ $ordenes->total() }} orden(es) de examen</p>
            </div>
        </div>
        <a href="{{ route('laboratorio.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            Nueva orden
        </a>
    </div>

    @include('partials.flash')

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])</span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar N° o paciente..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-brand-300 focus:ring-2 focus:ring-brand-100 outline-none transition">
                </div>
                <select name="estado" onchange="this.form.submit()" class="px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
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
                        <th class="px-6 py-3 font-semibold">N°</th>
                        <th class="px-6 py-3 font-semibold">Fecha</th>
                        <th class="px-6 py-3 font-semibold">Paciente</th>
                        <th class="px-6 py-3 font-semibold">Examenes</th>
                        <th class="px-6 py-3 font-semibold">Estado</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ordenes as $o)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-semibold text-slate-700">{{ $o->numero }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $o->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $o->paciente?->nombre_completo ?? '—' }}</td>
                            <td class="px-6 py-3.5"><span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600">{{ $o->items_count }} examen(es)</span></td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$o->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $o->estadoLabel() }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('laboratorio.show', $o) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Ver / Imprimir</a>
                                    <a href="{{ route('laboratorio.edit', $o) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('laboratorio.destroy', $o) }}" onsubmit="return confirm('Eliminar esta orden?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">No hay ordenes de laboratorio.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $ordenes->links() }}</div>
    </div>

@endsection
