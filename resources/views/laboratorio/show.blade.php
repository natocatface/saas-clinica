@extends('layouts.app')
@section('title', 'Orden '.$orden->numero)

@php
    $estadoColor = [
        'solicitada' => 'bg-sky-100 text-sky-700',
        'en_proceso' => 'bg-amber-100 text-amber-700',
        'completada' => 'bg-emerald-100 text-emerald-700',
        'entregada' => 'bg-brand-100 text-brand-700',
    ];
@endphp

@push('head')
<style>
@media print {
    #sidebar, header, footer, .no-print { display: none !important; }
    .lg\:pl-72 { padding-left: 0 !important; }
    body { background: #fff !important; }
    .print-card { box-shadow: none !important; }
    main { padding: 0 !important; }
}
</style>
@endpush

@section('content')
    <div class="no-print flex flex-wrap items-center justify-between gap-3 mb-6">
        <nav class="text-sm text-slate-400">
            <a href="{{ route('laboratorio.index') }}" class="hover:text-brand-600">Laboratorio</a>
            <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">{{ $orden->numero }}</span>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('laboratorio.edit', $orden) }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Editar</a>
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">Imprimir</button>
        </div>
    </div>

    <div class="print-card max-w-3xl mx-auto rounded-3xl bg-white shadow-card p-8 sm:p-10">
        <div class="flex items-start justify-between border-b border-slate-200 pb-5 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-brand-600 text-white grid place-items-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold text-slate-800">{{ config('app.name') }}</p>
                    <p class="text-xs text-slate-400">Orden de Laboratorio</p>
                </div>
            </div>
            <div class="text-right text-sm">
                <p class="font-bold text-slate-800">{{ $orden->numero }}</p>
                <p class="text-slate-400">{{ $orden->fecha->format('d/m/Y') }}</p>
                <span class="inline-block mt-1 text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$orden->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $orden->estadoLabel() }}</span>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Paciente</p>
                <p class="font-semibold text-slate-800">{{ $orden->paciente?->nombre_completo }}</p>
                <p class="text-slate-500">{{ $orden->paciente?->ci ? 'CI: '.$orden->paciente->ci : '' }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-xs uppercase tracking-wide text-slate-400">Medico solicitante</p>
                <p class="font-semibold text-slate-800">Dr(a). {{ $orden->medico?->nombre_completo ?? '—' }}</p>
            </div>
        </div>

        <table class="w-full text-sm mb-6">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-slate-400 border-b border-slate-200">
                    <th class="py-2 font-semibold">Examen</th>
                    <th class="py-2 font-semibold">Resultado</th>
                    <th class="py-2 font-semibold">Unidad</th>
                    <th class="py-2 font-semibold">Valor referencia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($orden->items as $item)
                    <tr>
                        <td class="py-2.5 font-medium text-slate-700">{{ $item->examen }}</td>
                        <td class="py-2.5 text-slate-700">{{ $item->resultado ?: '—' }}</td>
                        <td class="py-2.5 text-slate-500">{{ $item->unidad ?: '—' }}</td>
                        <td class="py-2.5 text-slate-500">{{ $item->valor_referencia ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if ($orden->observaciones)
            <div class="text-sm border-t border-slate-200 pt-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Observaciones</p>
                <p class="text-slate-700 whitespace-pre-line">{{ $orden->observaciones }}</p>
            </div>
        @endif
    </div>
@endsection
