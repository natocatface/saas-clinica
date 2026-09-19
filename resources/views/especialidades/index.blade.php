@extends('layouts.app')
@section('title', 'Especialidades')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'grid', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Especialidades</h1>
                <p class="text-sm text-slate-500">{{ $especialidades->total() }} especialidad(es) registradas</p>
            </div>
        </div>
        <a href="{{ route('especialidades.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            Nueva especialidad
        </a>
    </div>

    @include('partials.flash')

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="relative max-w-sm">
                <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar especialidad..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-brand-300 focus:ring-2 focus:ring-brand-100 outline-none transition">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Especialidad</th>
                        <th class="px-6 py-3 font-semibold">Tarifa</th>
                        <th class="px-6 py-3 font-semibold">Medicos</th>
                        <th class="px-6 py-3 font-semibold">Estado</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($especialidades as $esp)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-3 h-3 rounded-full" style="background: {{ $esp->color }}"></span>
                                    <div>
                                        <p class="font-semibold text-slate-700">{{ $esp->nombre }}</p>
                                        <p class="text-xs text-slate-400 truncate max-w-xs">{{ $esp->descripcion }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 font-medium text-slate-600">{{ money($esp->tarifa) }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $esp->medicos()->count() }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $esp->activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $esp->activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('especialidades.edit', $esp) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('especialidades.destroy', $esp) }}" onsubmit="return confirm('Eliminar esta especialidad?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">No hay especialidades registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $especialidades->links() }}</div>
    </div>

@endsection
