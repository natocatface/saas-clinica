@extends('layouts.superadmin')
@section('title', 'Clinicas')

@php
    $estadoColor = [
        'prueba' => 'bg-sky-100 text-sky-700',
        'activa' => 'bg-emerald-100 text-emerald-700',
        'suspendida' => 'bg-amber-100 text-amber-700',
        'cancelada' => 'bg-rose-100 text-rose-700',
    ];
@endphp

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Clinicas</h1>
            <p class="text-sm text-slate-500">{{ $clinicas->total() }} cuenta(s) en la plataforma</p>
        </div>
        <a href="{{ route('superadmin.clinicas.create') }}" class="px-4 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition shadow-glow flex items-center gap-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            Nueva clinica
        </a>
    </div>

    @include('partials.flash')

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-3 w-full">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])</span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar clinica..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-iris-400 outline-none transition">
                </div>
                <select name="estado" onchange="this.form.submit()" class="px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $k => $v)
                        <option value="{{ $k }}" {{ $estado === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2.5 rounded-full bg-iris-50 text-iris-600 text-sm font-medium hover:bg-iris-100 transition">Filtrar</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Clinica</th>
                        <th class="px-6 py-3 font-semibold">Plan</th>
                        <th class="px-6 py-3 font-semibold">Usuarios</th>
                        <th class="px-6 py-3 font-semibold">Estado</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clinicas as $c)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl grid place-items-center text-white text-xs font-bold" style="background: {{ $c->color }}">{{ strtoupper(substr($c->nombre,0,2)) }}</span>
                                    <div>
                                        <p class="font-semibold text-slate-700">{{ $c->nombre }}</p>
                                        <p class="text-xs text-slate-400">{{ $c->email ?? $c->ciudad }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $c->plan?->nombre ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $c->usuarios_count }}</td>
                            <td class="px-6 py-3.5"><span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$c->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $c->estadoLabel() }}</span></td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('superadmin.clinicas.impersonar', $c) }}">
                                        @csrf
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition">Entrar como</button>
                                    </form>
                                    <a href="{{ route('superadmin.clinicas.show', $c) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Ver</a>
                                    <a href="{{ route('superadmin.clinicas.edit', $c) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-iris-600 bg-iris-50 hover:bg-iris-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('superadmin.clinicas.destroy', $c) }}" onsubmit="return confirm('Eliminar esta clinica y todos sus datos?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">No hay clinicas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $clinicas->links() }}</div>
    </div>

@endsection
