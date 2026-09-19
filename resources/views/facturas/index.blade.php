@extends('layouts.app')
@section('title', 'Facturacion y Pagos')

@php
    $estadoColor = [
        'pendiente' => 'bg-amber-100 text-amber-700',
        'pagada' => 'bg-emerald-100 text-emerald-700',
        'anulada' => 'bg-rose-100 text-rose-700',
    ];
    $sunatColor = [
        'no_enviado' => 'bg-slate-100 text-slate-500',
        'pendiente'  => 'bg-amber-100 text-amber-700',
        'aceptado'   => 'bg-emerald-100 text-emerald-700',
        'observado'  => 'bg-sky-100 text-sky-700',
        'rechazado'  => 'bg-rose-100 text-rose-700',
        'anulado'    => 'bg-slate-200 text-slate-600',
    ];
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'cash', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Facturacion y Pagos</h1>
                <p class="text-sm text-slate-500">{{ $facturas->total() }} factura(s)</p>
            </div>
        </div>
        <a href="{{ route('facturas.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            Nueva factura
        </a>
    </div>

    @include('partials.flash')

    <!-- Resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">Cobrado (pagadas)</p>
            <p class="text-3xl font-extrabold mt-1">{{ money($resumen['total']) }}</p>
        </div>
        <div class="rounded-3xl bg-gradient-to-br from-amber-500 to-orange-500 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">Por cobrar (pendientes)</p>
            <p class="text-3xl font-extrabold mt-1">{{ money($resumen['pendiente']) }}</p>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-card">
            <p class="text-sm text-slate-400">Total facturas</p>
            <p class="text-3xl font-extrabold mt-1 text-slate-800">{{ $resumen['count'] }}</p>
        </div>
    </div>

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
                        <th class="px-6 py-3 font-semibold">Total</th>
                        <th class="px-6 py-3 font-semibold">Estado</th>
                        <th class="px-6 py-3 font-semibold">SUNAT</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($facturas as $f)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-semibold text-slate-700">{{ $f->numero }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $f->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $f->paciente?->nombre_completo ?? '—' }}</td>
                            <td class="px-6 py-3.5 font-semibold text-slate-700">{{ money($f->total) }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$f->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $f->estadoLabel() }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $sunatColor[$f->sunat_estado] ?? 'bg-slate-100 text-slate-500' }}">{{ $f->sunatEstadoLabel() }}</span>
                                @if ($f->comprobante())
                                    <span class="block text-[11px] text-slate-400 mt-1">{{ $f->comprobante() }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($f->estado === 'pendiente')
                                        <form method="POST" action="{{ route('facturas.pagar', $f) }}">
                                            @csrf @method('PATCH')
                                            <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition">Cobrar</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('facturas.show', $f) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Ver</a>
                                    <a href="{{ route('facturas.edit', $f) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('facturas.destroy', $f) }}" onsubmit="return confirm('Eliminar esta factura?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">No hay facturas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $facturas->links() }}</div>
    </div>

@endsection
