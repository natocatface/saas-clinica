@extends('layouts.app')
@section('title', 'Papelera')

@section('content')

    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
            @include('partials.icon', ['name' => 'box', 'class' => 'w-6 h-6'])
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Papelera</h1>
            <p class="text-sm text-slate-500">Registros eliminados · puedes restaurarlos o borrarlos definitivamente</p>
        </div>
    </div>

    @include('partials.flash')

    <!-- Pestanas por tipo -->
    <div class="flex flex-wrap gap-2 mb-5">
        @foreach ($tipos as $k => $t)
            <a href="{{ route('papelera.index', ['tipo' => $k]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ $tipo === $k ? 'bg-brand-600 text-white shadow-lg shadow-brand-500/30' : 'bg-white border border-slate-200 text-slate-600 hover:border-brand-300' }}">
                {{ $t['label'] }}
                <span class="ml-1 text-xs {{ $tipo === $k ? 'text-white/80' : 'text-slate-400' }}">({{ $conteos[$k] }})</span>
            </a>
        @endforeach
    </div>

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                        <th class="px-6 py-3 font-semibold">Registro</th>
                        <th class="px-6 py-3 font-semibold">Eliminado</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($registros as $r)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-medium text-slate-700">{{ $r['descripcion'] }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ optional($r['eliminado'])->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('papelera.restaurar', [$tipo, $r['id']]) }}">
                                        @csrf @method('PUT')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 transition">Restaurar</button>
                                    </form>
                                    <form method="POST" action="{{ route('papelera.forzar', [$tipo, $r['id']]) }}" onsubmit="return confirm('Eliminar DEFINITIVAMENTE? Esta accion no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar definitivo</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-12 text-center text-slate-400">No hay registros eliminados en esta seccion.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $registros->links() }}</div>
    </div>

@endsection
