@extends('layouts.app')
@section('title', 'Atencion clinica')

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('historias.index') }}" class="hover:text-brand-600">Historias Clinicas</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">{{ $historia->paciente?->nombre_completo }}</span>
    </nav>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-2xl font-extrabold text-slate-800">Atencion del {{ $historia->fecha->format('d/m/Y') }}</h1>
        <a href="{{ route('historias.edit', $historia) }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">Editar</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="rounded-3xl bg-white shadow-card p-6">
            <p class="text-xs uppercase tracking-wide text-slate-400 mb-3">Paciente</p>
            <div class="flex items-center gap-3 mb-5">
                <span class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-700 grid place-items-center font-bold">
                    {{ strtoupper(substr($historia->paciente->nombres,0,1).substr($historia->paciente->apellidos,0,1)) }}
                </span>
                <div>
                    <p class="font-bold text-slate-800">{{ $historia->paciente?->nombre_completo }}</p>
                    <p class="text-xs text-slate-400">{{ $historia->paciente?->ci }}</p>
                </div>
            </div>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Medico</dt><dd class="font-medium text-slate-700">Dr(a). {{ $historia->medico?->nombre_completo ?? '—' }}</dd></div>
                <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Peso</dt><dd class="font-medium text-slate-700">{{ $historia->peso ? $historia->peso.' kg' : '—' }}</dd></div>
                <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Talla</dt><dd class="font-medium text-slate-700">{{ $historia->talla ? $historia->talla.' cm' : '—' }}</dd></div>
                <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">IMC</dt><dd class="font-medium text-slate-700">{{ $historia->imc ?? '—' }}</dd></div>
                <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">P. arterial</dt><dd class="font-medium text-slate-700">{{ $historia->presion_arterial ?? '—' }}</dd></div>
                <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Temperatura</dt><dd class="font-medium text-slate-700">{{ $historia->temperatura ? $historia->temperatura.' C' : '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-slate-400">F. cardiaca</dt><dd class="font-medium text-slate-700">{{ $historia->frecuencia_cardiaca ? $historia->frecuencia_cardiaca.' lpm' : '—' }}</dd></div>
            </dl>
        </div>

        <div class="lg:col-span-2 rounded-3xl bg-white shadow-card p-6 space-y-5">
            @foreach ([
                'Motivo de consulta' => $historia->motivo_consulta,
                'Sintomas' => $historia->sintomas,
                'Diagnostico' => $historia->diagnostico,
                'Tratamiento' => $historia->tratamiento,
                'Observaciones' => $historia->observaciones,
            ] as $titulo => $texto)
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">{{ $titulo }}</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $texto ?: '—' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Archivos adjuntos -->
    <div class="mt-6 rounded-3xl bg-white shadow-card p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <h2 class="font-bold text-slate-800">Archivos adjuntos <span class="text-slate-400 font-normal">({{ $historia->archivos->count() }})</span></h2>
            <form method="POST" action="{{ route('historias.archivos.store', $historia) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                @csrf
                <input type="file" name="archivo" required
                       class="text-sm text-slate-600 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-brand-50 file:text-brand-600 file:text-sm file:font-medium hover:file:bg-brand-100">
                <button class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">Subir</button>
            </form>
        </div>

        @error('archivo')<p class="mb-3 text-sm text-rose-600">{{ $message }}</p>@enderror

        @if ($historia->archivos->isEmpty())
            <p class="text-sm text-slate-400 py-6 text-center">Aun no hay archivos. Adjunta estudios, imagenes o documentos (PDF, JPG, PNG, DOC; max 10 MB).</p>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach ($historia->archivos as $arch)
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-100 p-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 grid place-items-center shrink-0">
                            @include('partials.icon', ['name' => 'clipboard', 'class' => 'w-5 h-5'])
                        </div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ $arch->url() }}" target="_blank" class="block text-sm font-medium text-slate-700 truncate hover:text-brand-600">{{ $arch->nombre }}</a>
                            <p class="text-xs text-slate-400">{{ $arch->tamanoLegible() }} · {{ $arch->created_at?->format('d/m/Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('historias.archivos.destroy', [$historia, $arch]) }}" onsubmit="return confirm('Eliminar este archivo?')">
                            @csrf @method('DELETE')
                            <button class="text-rose-500 hover:text-rose-700 transition" title="Eliminar">✕</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
