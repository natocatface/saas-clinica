@extends('layouts.app')
@section('title', 'Farmacia e Inventario')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'box', 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Farmacia e Inventario</h1>
                <p class="text-sm text-slate-500">{{ $productos->total() }} producto(s)</p>
            </div>
        </div>
        <a href="{{ route('productos.create') }}" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            Nuevo producto
        </a>
    </div>

    @include('partials.flash')

    <!-- Resumen -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="rounded-3xl bg-white p-5 shadow-card">
            <p class="text-sm text-slate-400">Productos</p>
            <p class="text-3xl font-extrabold mt-1 text-slate-800">{{ $resumen['total'] }}</p>
        </div>
        <a href="{{ route('productos.index', ['filtro' => 'bajo']) }}" class="rounded-3xl bg-gradient-to-br from-rose-500 to-rose-600 text-white p-5 shadow-card block">
            <p class="text-sm text-white/80">Stock bajo</p>
            <p class="text-3xl font-extrabold mt-1">{{ $resumen['bajo'] }}</p>
        </a>
        <div class="rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-5 shadow-card">
            <p class="text-sm text-white/80">Valor inventario (venta)</p>
            <p class="text-3xl font-extrabold mt-1">{{ money($resumen['valor']) }}</p>
        </div>
    </div>

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
            <form method="GET" class="relative flex-1 min-w-[200px] max-w-sm">
                <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])</span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Buscar producto, codigo o categoria..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-brand-300 focus:ring-2 focus:ring-brand-100 outline-none transition">
            </form>
            @if ($filtro === 'bajo')
                <a href="{{ route('productos.index') }}" class="px-3 py-1.5 rounded-full text-xs font-medium bg-rose-50 text-rose-600 hover:bg-rose-100 transition">Filtro: stock bajo ✕</a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Producto</th>
                        <th class="px-6 py-3 font-semibold">Categoria</th>
                        <th class="px-6 py-3 font-semibold">Stock</th>
                        <th class="px-6 py-3 font-semibold">P. Venta</th>
                        <th class="px-6 py-3 font-semibold">Vence</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($productos as $p)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5">
                                <p class="font-semibold text-slate-700">{{ $p->nombre }}</p>
                                <p class="text-xs text-slate-400">{{ $p->codigo }}</p>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $p->categoria ?? '—' }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 font-semibold {{ $p->stock_bajo ? 'text-rose-600' : 'text-slate-700' }}">
                                    {{ $p->stock }} <span class="text-xs font-normal text-slate-400">{{ $p->unidad }}</span>
                                    @if ($p->stock_bajo)<span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-100 text-rose-600">bajo</span>@endif
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-slate-600">{{ money($p->precio_venta) }}</td>
                            <td class="px-6 py-3.5">
                                @if ($p->vencimiento)
                                    <span class="{{ $p->por_vencer ? 'text-amber-600 font-medium' : 'text-slate-500' }}">{{ $p->vencimiento->format('d/m/Y') }}</span>
                                @else <span class="text-slate-400">—</span> @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('productos.show', $p) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Stock</a>
                                    <a href="{{ route('productos.edit', $p) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-brand-600 bg-brand-50 hover:bg-brand-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('productos.destroy', $p) }}" onsubmit="return confirm('Eliminar este producto?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">No hay productos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4">{{ $productos->links() }}</div>
    </div>

@endsection
