@extends('layouts.app')
@section('title', $producto->nombre)

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('productos.index') }}" class="hover:text-brand-600">Farmacia</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">{{ $producto->nombre }}</span>
    </nav>

    @include('partials.flash')

    <div class="grid lg:grid-cols-3 gap-6">

        <!-- Ficha + movimiento -->
        <div class="space-y-6">
            <div class="rounded-3xl bg-white shadow-card p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-700 grid place-items-center">
                        @include('partials.icon', ['name' => 'box', 'class' => 'w-6 h-6'])
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $producto->nombre }}</p>
                        <p class="text-xs text-slate-400">{{ $producto->codigo }} · {{ $producto->categoria }}</p>
                    </div>
                </div>
                <div class="text-center py-4 rounded-2xl bg-slate-50 mb-4">
                    <p class="text-4xl font-extrabold {{ $producto->stock_bajo ? 'text-rose-600' : 'text-slate-800' }}">{{ $producto->stock }}</p>
                    <p class="text-sm text-slate-400">{{ $producto->unidad }} en stock @if ($producto->stock_bajo)· <span class="text-rose-600 font-medium">bajo minimo ({{ $producto->stock_minimo }})</span>@endif</p>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Precio compra</dt><dd class="font-medium text-slate-700">{{ money($producto->precio_compra) }}</dd></div>
                    <div class="flex justify-between border-b border-slate-50 pb-2"><dt class="text-slate-400">Precio venta</dt><dd class="font-medium text-slate-700">{{ money($producto->precio_venta) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-400">Vencimiento</dt><dd class="font-medium {{ $producto->por_vencer ? 'text-amber-600' : 'text-slate-700' }}">{{ optional($producto->vencimiento)->format('d/m/Y') ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="rounded-3xl bg-white shadow-card p-6">
                <p class="font-bold text-slate-800 mb-4">Registrar movimiento</p>
                <form method="POST" action="{{ route('productos.movimiento', $producto) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <select name="tipo" class="px-3 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 outline-none">
                            <option value="entrada">Entrada (+)</option>
                            <option value="salida">Salida (−)</option>
                        </select>
                        <input type="number" min="1" name="cantidad" placeholder="Cantidad" required
                               class="px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    </div>
                    <input type="text" name="motivo" placeholder="Motivo (compra, dispensacion...)"
                           class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    <button class="w-full py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">Registrar</button>
                </form>
            </div>
        </div>

        <!-- Historial -->
        <div class="lg:col-span-2 rounded-3xl bg-white shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100"><h2 class="font-bold text-slate-800">Historial de movimientos</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                            <th class="px-6 py-3 font-semibold">Fecha</th>
                            <th class="px-6 py-3 font-semibold">Tipo</th>
                            <th class="px-6 py-3 font-semibold">Cantidad</th>
                            <th class="px-6 py-3 font-semibold">Motivo</th>
                            <th class="px-6 py-3 font-semibold">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($producto->movimientos as $mov)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3 text-slate-500">{{ $mov->fecha->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $mov->tipo === 'entrada' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $mov->tipo === 'entrada' ? 'Entrada' : 'Salida' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 font-semibold {{ $mov->tipo === 'entrada' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $mov->tipo === 'entrada' ? '+' : '−' }}{{ $mov->cantidad }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $mov->motivo ?? '—' }}</td>
                                <td class="px-6 py-3 text-slate-500">{{ $mov->user?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">Sin movimientos registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
