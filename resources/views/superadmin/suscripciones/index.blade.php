@extends('layouts.superadmin')
@section('title', 'Suscripciones')

@php
    $subColor = ['activa'=>'bg-emerald-100 text-emerald-700','vencida'=>'bg-amber-100 text-amber-700','cancelada'=>'bg-rose-100 text-rose-700'];
@endphp

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Suscripciones</h1>
            <p class="text-sm text-slate-500">{{ $suscripciones->total() }} suscripcion(es)</p>
        </div>
        <a href="{{ route('superadmin.suscripciones.create') }}" class="px-4 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition shadow-glow flex items-center gap-2">
            @include('partials.icon', ['name'=>'plus','class'=>'w-4 h-4']) Nueva suscripcion
        </a>
    </div>

    @include('partials.flash')

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-5 shadow-card"><p class="text-sm text-white/80">Activas</p><p class="text-3xl font-extrabold mt-1">{{ $resumen['activas'] }}</p></div>
        <div class="rounded-3xl bg-gradient-to-br from-acc-600 to-acc-500 text-white p-5 shadow-card"><p class="text-sm text-white/80">MRR</p><p class="text-3xl font-extrabold mt-1">Bs {{ number_format($resumen['mrr'], 0) }}</p></div>
        <div class="rounded-3xl bg-white p-5 shadow-card"><p class="text-sm text-slate-400">Total</p><p class="text-3xl font-extrabold mt-1 text-slate-800">{{ $resumen['total'] }}</p></div>
    </div>

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="flex items-center gap-3">
                <select name="estado" onchange="this.form.submit()" class="px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $k=>$v)<option value="{{ $k }}" {{ $estado===$k?'selected':'' }}>{{ $v }}</option>@endforeach
                </select>
                <button class="px-4 py-2.5 rounded-full bg-iris-50 text-iris-600 text-sm font-medium hover:bg-iris-100 transition">Filtrar</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                    <th class="px-6 py-3 font-semibold">Clinica</th><th class="px-6 py-3 font-semibold">Plan</th><th class="px-6 py-3 font-semibold">Monto</th><th class="px-6 py-3 font-semibold">Ciclo</th><th class="px-6 py-3 font-semibold">Vigencia</th><th class="px-6 py-3 font-semibold">Estado</th><th class="px-6 py-3 font-semibold text-right">Acciones</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($suscripciones as $s)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-medium text-slate-700">{{ $s->clinica?->nombre ?? '—' }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $s->plan?->nombre ?? '—' }}</td>
                            <td class="px-6 py-3.5 font-semibold text-slate-700">Bs {{ number_format($s->monto, 2) }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ ucfirst($s->ciclo) }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $s->inicio?->format('d/m/Y') }} — {{ $s->fin?->format('d/m/Y') ?? '∞' }}</td>
                            <td class="px-6 py-3.5"><span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $subColor[$s->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $s->estadoLabel() }}</span></td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.suscripciones.edit', $s) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-iris-600 bg-iris-50 hover:bg-iris-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('superadmin.suscripciones.destroy', $s) }}" onsubmit="return confirm('Eliminar suscripcion?')">@csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">No hay suscripciones.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $suscripciones->links() }}</div>
    </div>
@endsection
