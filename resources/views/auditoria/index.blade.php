@extends('layouts.app')
@section('title', 'Auditoria')

@php
    $accionColor = [
        'crear' => 'bg-emerald-100 text-emerald-700',
        'editar' => 'bg-sky-100 text-sky-700',
        'eliminar' => 'bg-rose-100 text-rose-700',
    ];
@endphp

@section('content')

    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
            @include('partials.icon', ['name' => 'shield', 'class' => 'w-6 h-6'])
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Auditoria</h1>
            <p class="text-sm text-slate-500">Bitacora de acciones · {{ $auditorias->total() }} registro(s)</p>
        </div>
    </div>

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <select name="accion" onchange="this.form.submit()" class="px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                    <option value="">Todas las acciones</option>
                    @foreach ($acciones as $k => $v)
                        <option value="{{ $k }}" {{ $accion === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                <select name="modelo" onchange="this.form.submit()" class="px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                    <option value="">Todos los modulos</option>
                    @foreach ($modelos as $m)
                        <option value="{{ $m }}" {{ $modelo === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Fecha</th>
                        <th class="px-6 py-3 font-semibold">Usuario</th>
                        <th class="px-6 py-3 font-semibold">Accion</th>
                        <th class="px-6 py-3 font-semibold">Modulo</th>
                        <th class="px-6 py-3 font-semibold">Detalle</th>
                        <th class="px-6 py-3 font-semibold">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($auditorias as $a)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3 text-slate-500">{{ $a->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3 font-medium text-slate-700">{{ $a->user_nombre ?? '—' }}</td>
                            <td class="px-6 py-3"><span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $accionColor[$a->accion] ?? 'bg-slate-100 text-slate-600' }}">{{ $a->accionLabel() }}</span></td>
                            <td class="px-6 py-3 text-slate-500">{{ $a->modelo }} #{{ $a->modelo_id }}</td>
                            <td class="px-6 py-3 text-slate-500">{{ $a->descripcion ?? '—' }}</td>
                            <td class="px-6 py-3 text-slate-400 text-xs">{{ $a->ip ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">Aun no hay registros de auditoria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $auditorias->links() }}</div>
    </div>

@endsection
