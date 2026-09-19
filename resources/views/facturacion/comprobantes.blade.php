@extends('layouts.app')
@section('title', 'Comprobantes Electronicos')

@php
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
                @include('partials.icon', ['name' => 'receipt', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Comprobantes Electronicos</h1>
                <p class="text-sm text-slate-500">{{ $comprobantes->total() }} comprobante(s) emitido(s)</p>
            </div>
        </div>
        <a href="{{ route('facturacion.comprobantes', array_merge($filtros, ['export' => 'csv'])) }}"
           class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-600 hover:border-brand-300 hover:text-brand-600 transition flex items-center gap-2">
            @include('partials.icon', ['name' => 'arrow-down', 'class' => 'w-4 h-4'])
            Exportar CSV
        </a>
    </div>

    @include('partials.flash')

    <!-- Resumen -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        <div class="rounded-2xl bg-white p-4 shadow-card">
            <p class="text-xs text-slate-400">Total</p>
            <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $resumen['total'] }}</p>
        </div>
        <div class="rounded-2xl bg-emerald-50 p-4 shadow-card">
            <p class="text-xs text-emerald-600">Aceptados</p>
            <p class="text-2xl font-extrabold text-emerald-700 mt-1">{{ $resumen['aceptado'] }}</p>
        </div>
        <div class="rounded-2xl bg-amber-50 p-4 shadow-card">
            <p class="text-xs text-amber-600">Pendientes</p>
            <p class="text-2xl font-extrabold text-amber-700 mt-1">{{ $resumen['pendiente'] }}</p>
        </div>
        <div class="rounded-2xl bg-rose-50 p-4 shadow-card">
            <p class="text-xs text-rose-600">Rechazados</p>
            <p class="text-2xl font-extrabold text-rose-700 mt-1">{{ $resumen['rechazado'] }}</p>
        </div>
        <div class="rounded-2xl bg-slate-100 p-4 shadow-card">
            <p class="text-xs text-slate-500">Anulados</p>
            <p class="text-2xl font-extrabold text-slate-700 mt-1">{{ $resumen['anulado'] }}</p>
        </div>
    </div>

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="flex flex-col sm:flex-row sm:flex-wrap sm:items-end gap-3">
                <div class="relative w-full sm:flex-1 sm:min-w-[180px] sm:max-w-xs">
                    <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])</span>
                    <input type="text" name="q" value="{{ $filtros['q'] }}" placeholder="Serie, N° o paciente..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-brand-300 focus:ring-2 focus:ring-brand-100 outline-none transition">
                </div>
                <select name="estado" class="w-full sm:w-auto px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $k => $v)
                        <option value="{{ $k }}" {{ $filtros['estado'] === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <select name="tipo" class="w-full sm:w-auto px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                    <option value="">Todos los tipos</option>
                    @foreach ($tipos as $k => $v)
                        <option value="{{ $k }}" {{ $filtros['tipo'] === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <div class="flex gap-3">
                    <input type="date" name="desde" value="{{ $filtros['desde'] }}" class="w-full sm:w-auto px-3 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] }}" class="w-full sm:w-auto px-3 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                </div>
                <button class="w-full sm:w-auto px-4 py-2.5 rounded-full bg-brand-50 text-brand-600 text-sm font-medium hover:bg-brand-100 transition">Filtrar</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Comprobante</th>
                        <th class="px-6 py-3 font-semibold">Tipo</th>
                        <th class="px-6 py-3 font-semibold">Fecha</th>
                        <th class="px-6 py-3 font-semibold">Paciente</th>
                        <th class="px-6 py-3 font-semibold text-right">Total</th>
                        <th class="px-6 py-3 font-semibold">SUNAT</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($comprobantes as $f)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-semibold text-slate-700">{{ $f->comprobante() }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $f->tipoComprobanteLabel() }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $f->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $f->paciente?->nombre_completo ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-right font-semibold text-slate-700">{{ money($f->total) }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $sunatColor[$f->sunat_estado] ?? 'bg-slate-100 text-slate-500' }}">{{ $f->sunatEstadoLabel() }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($f->xml_path)
                                        <a href="{{ route('facturas.descargar', [$f, 'xml']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">XML</a>
                                    @endif
                                    @if ($f->cdr_path)
                                        <a href="{{ route('facturas.descargar', [$f, 'cdr']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">CDR</a>
                                    @endif
                                    <a href="{{ route('facturas.show', $f) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Ver</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">No hay comprobantes que coincidan con el filtro.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $comprobantes->links() }}</div>
    </div>
@endsection
