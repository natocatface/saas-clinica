@extends('layouts.app')
@section('title', 'Pacientes')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'users', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Pacientes</h1>
                <p class="text-sm text-slate-500">{{ $pacientes->total() }} paciente(s) registrados</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pacientes.export', ['q' => $q]) }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-brand-300 hover:text-brand-600 transition shadow-sm">
                Exportar CSV
            </a>
            <a href="{{ route('pacientes.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
                @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
                Nuevo paciente
            </a>
        </div>
    </div>

    @include('partials.flash')

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="relative max-w-sm">
                <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por nombre, CI o telefono..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-brand-300 focus:ring-2 focus:ring-brand-100 outline-none transition">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Paciente</th>
                        <th class="px-6 py-3 font-semibold">CI</th>
                        <th class="px-6 py-3 font-semibold">Edad</th>
                        <th class="px-6 py-3 font-semibold">Telefono</th>
                        <th class="px-6 py-3 font-semibold">Sangre</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pacientes as $pac)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 grid place-items-center text-xs font-bold">
                                        {{ strtoupper(substr($pac->nombres,0,1).substr($pac->apellidos,0,1)) }}
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-700">{{ $pac->nombre_completo }}</p>
                                        <p class="text-xs text-slate-400">{{ $pac->email ?? $pac->seguro }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $pac->ci ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $pac->edad !== null ? $pac->edad.' años' : '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $pac->telefono ?? '—' }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-block text-xs font-semibold px-2 py-1 rounded-lg bg-rose-50 text-rose-600">{{ $pac->grupo_sanguineo ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('pacientes.show', $pac) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Ver</a>
                                    <a href="{{ route('pacientes.edit', $pac) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('pacientes.destroy', $pac) }}" onsubmit="return confirm('Eliminar este paciente?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">No hay pacientes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $pacientes->links() }}</div>
    </div>

@endsection
