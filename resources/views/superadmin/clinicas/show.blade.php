@extends('layouts.superadmin')
@section('title', $clinica->nombre)

@php
    $estadoColor = [
        'prueba' => 'bg-sky-100 text-sky-700',
        'activa' => 'bg-emerald-100 text-emerald-700',
        'suspendida' => 'bg-amber-100 text-amber-700',
        'cancelada' => 'bg-rose-100 text-rose-700',
    ];
    $subColor = [
        'activa' => 'bg-emerald-100 text-emerald-700',
        'vencida' => 'bg-amber-100 text-amber-700',
        'cancelada' => 'bg-rose-100 text-rose-700',
    ];
@endphp

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('superadmin.clinicas.index') }}" class="hover:text-iris-600">Clinicas</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">{{ $clinica->nombre }}</span>
    </nav>

    @include('partials.flash')

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Ficha + estado -->
        <div class="space-y-6">
            <div class="rounded-3xl bg-white shadow-card p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-14 h-14 rounded-2xl grid place-items-center text-white text-lg font-bold" style="background: {{ $clinica->color }}">{{ strtoupper(substr($clinica->nombre,0,2)) }}</span>
                    <div>
                        <p class="font-bold text-slate-800">{{ $clinica->nombre }}</p>
                        <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$clinica->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $clinica->estadoLabel() }}</span>
                    </div>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Plan</dt><dd class="font-medium text-slate-700">{{ $clinica->plan?->nombre ?? '—' }}</dd></div>
                    <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">NIT</dt><dd class="font-medium text-slate-700">{{ $clinica->nit ?? '—' }}</dd></div>
                    <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Email</dt><dd class="font-medium text-slate-700">{{ $clinica->email ?? '—' }}</dd></div>
                    <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Telefono</dt><dd class="font-medium text-slate-700">{{ $clinica->telefono ?? '—' }}</dd></div>
                    <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Ciudad</dt><dd class="font-medium text-slate-700">{{ $clinica->ciudad ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Alta</dt><dd class="font-medium text-slate-700">{{ $clinica->created_at?->format('d/m/Y') }}</dd></div>
                </dl>
                <a href="{{ route('superadmin.clinicas.edit', $clinica) }}" class="mt-5 block text-center px-4 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition">Editar clinica</a>
                <form method="POST" action="{{ route('superadmin.clinicas.impersonar', $clinica) }}" class="mt-2">
                    @csrf
                    <button class="w-full px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">Entrar como esta clinica</button>
                </form>
            </div>

            <div class="rounded-3xl bg-white shadow-card p-6">
                <p class="font-bold text-slate-800 mb-3">Cambiar estado</p>
                <form method="POST" action="{{ route('superadmin.clinicas.estado', $clinica) }}" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="estado" class="flex-1 px-3 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none">
                        @foreach (\App\Models\Clinica::ESTADOS as $k => $v)
                            <option value="{{ $k }}" {{ $clinica->estado === $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                    <button class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700 transition">Aplicar</button>
                </form>
            </div>
        </div>

        <!-- Usuarios y suscripciones -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-3xl bg-white shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100"><h2 class="font-bold text-slate-800">Usuarios ({{ $clinica->usuarios->count() }})</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                            <th class="px-6 py-3 font-semibold">Nombre</th><th class="px-6 py-3 font-semibold">Correo</th><th class="px-6 py-3 font-semibold">Rol</th><th class="px-6 py-3 font-semibold">Estado</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($clinica->usuarios as $u)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-3 font-medium text-slate-700">{{ $u->name }}</td>
                                    <td class="px-6 py-3 text-slate-500">{{ $u->email }}</td>
                                    <td class="px-6 py-3 text-slate-500">{{ $u->roleLabel() }}</td>
                                    <td class="px-6 py-3"><span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $u->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $u->is_active ? 'Activo' : 'Inactivo' }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Sin usuarios.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl bg-white shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100"><h2 class="font-bold text-slate-800">Suscripciones</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                            <th class="px-6 py-3 font-semibold">Plan</th><th class="px-6 py-3 font-semibold">Monto</th><th class="px-6 py-3 font-semibold">Ciclo</th><th class="px-6 py-3 font-semibold">Vigencia</th><th class="px-6 py-3 font-semibold">Estado</th>
                        </tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($clinica->suscripciones as $s)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-3 font-medium text-slate-700">{{ $s->plan?->nombre ?? '—' }}</td>
                                    <td class="px-6 py-3 text-slate-500">Bs {{ number_format($s->monto, 2) }}</td>
                                    <td class="px-6 py-3 text-slate-500">{{ ucfirst($s->ciclo) }}</td>
                                    <td class="px-6 py-3 text-slate-500">{{ $s->inicio?->format('d/m/Y') }} — {{ $s->fin?->format('d/m/Y') ?? '∞' }}</td>
                                    <td class="px-6 py-3"><span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $subColor[$s->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $s->estadoLabel() }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Sin suscripciones.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
