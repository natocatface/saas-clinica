@extends('layouts.app')
@section('title', 'Receta')

@push('head')
<style>
@media print {
    #sidebar, header, footer, .no-print { display: none !important; }
    .lg\:pl-72 { padding-left: 0 !important; }
    body { background: #fff !important; }
    .print-card { box-shadow: none !important; border: none !important; }
    main { padding: 0 !important; }
}
</style>
@endpush

@section('content')
    <div class="no-print flex flex-wrap items-center justify-between gap-3 mb-6">
        <nav class="text-sm text-slate-400">
            <a href="{{ route('recetas.index') }}" class="hover:text-brand-600">Recetas</a>
            <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">Receta #{{ $receta->id }}</span>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('recetas.edit', $receta) }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Editar</a>
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">Imprimir</button>
        </div>
    </div>

    <div class="print-card max-w-3xl mx-auto rounded-3xl bg-white shadow-card p-8 sm:p-10">
        <!-- Encabezado -->
        <div class="flex items-start justify-between border-b border-slate-200 pb-5 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-brand-600 text-white grid place-items-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold text-slate-800">{{ config('app.name') }}</p>
                    <p class="text-xs text-slate-400">Receta Medica</p>
                </div>
            </div>
            <div class="text-right text-sm">
                <p class="font-semibold text-slate-700">N° {{ str_pad($receta->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-slate-400">{{ $receta->fecha->format('d/m/Y') }}</p>
            </div>
        </div>

        <!-- Datos -->
        <div class="grid sm:grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Paciente</p>
                <p class="font-semibold text-slate-800">{{ $receta->paciente?->nombre_completo }}</p>
                <p class="text-slate-500">{{ $receta->paciente?->ci ? 'CI: '.$receta->paciente->ci : '' }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-xs uppercase tracking-wide text-slate-400">Medico</p>
                <p class="font-semibold text-slate-800">Dr(a). {{ $receta->medico?->nombre_completo ?? '—' }}</p>
                <p class="text-slate-500">{{ $receta->medico?->matricula ? 'Mat: '.$receta->medico->matricula : '' }}</p>
            </div>
        </div>

        @if ($receta->diagnostico)
            <div class="mb-6 text-sm">
                <p class="text-xs uppercase tracking-wide text-slate-400">Diagnostico</p>
                <p class="text-slate-700">{{ $receta->diagnostico }}</p>
            </div>
        @endif

        <!-- Medicamentos -->
        <p class="text-2xl font-serif italic text-brand-600 mb-3">Rp/</p>
        <div class="space-y-3 mb-6">
            @foreach ($receta->items as $n => $item)
                <div class="flex gap-3 text-sm">
                    <span class="font-bold text-slate-400">{{ $n + 1 }}.</span>
                    <div>
                        <p class="font-semibold text-slate-800">{{ $item->medicamento }} <span class="font-normal text-slate-500">{{ $item->dosis }}</span></p>
                        <p class="text-slate-500">
                            {{ collect([$item->frecuencia, $item->duracion, $item->indicaciones])->filter()->implode(' · ') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($receta->notas)
            <div class="text-sm border-t border-slate-200 pt-4 mb-10">
                <p class="text-xs uppercase tracking-wide text-slate-400">Indicaciones generales</p>
                <p class="text-slate-700 whitespace-pre-line">{{ $receta->notas }}</p>
            </div>
        @endif

        <!-- Firma -->
        <div class="mt-12 text-center">
            <div class="mx-auto w-56 border-t border-slate-400 pt-2 text-sm text-slate-600">
                Dr(a). {{ $receta->medico?->nombre_completo ?? '_______________' }}<br>
                <span class="text-xs text-slate-400">Firma y sello</span>
            </div>
        </div>
    </div>
@endsection
