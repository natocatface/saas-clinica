@extends('layouts.app')
@section('title', 'Historias Clinicas')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'clipboard', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Historias Clinicas</h1>
                <p class="text-sm text-slate-500">{{ $historias->total() }} registro(s) clinicos</p>
            </div>
        </div>
        <a href="{{ route('historias.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            Nueva atencion
        </a>
    </div>

    @include('partials.flash')

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="relative max-w-sm">
                <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por paciente o CI..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-brand-300 focus:ring-2 focus:ring-brand-100 outline-none transition">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Fecha</th>
                        <th class="px-6 py-3 font-semibold">Paciente</th>
                        <th class="px-6 py-3 font-semibold">Medico</th>
                        <th class="px-6 py-3 font-semibold">Diagnostico</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($historias as $h)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-medium text-slate-700">{{ $h->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $h->paciente?->nombre_completo ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500">Dr(a). {{ $h->medico?->apellidos ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500 truncate max-w-xs">{{ $h->diagnostico ?? $h->motivo_consulta ?? '—' }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('historias.show', $h) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Ver</a>
                                    <a href="{{ route('historias.edit', $h) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('historias.destroy', $h) }}" onsubmit="return confirm('Eliminar este registro?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">No hay historias clinicas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $historias->links() }}</div>
    </div>

@endsection
