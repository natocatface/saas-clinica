@extends('layouts.superadmin')
@section('title', 'Planes')

@section('content')

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Planes</h1>
            <p class="text-sm text-slate-500">{{ $planes->total() }} plan(es) de suscripcion</p>
        </div>
        <a href="{{ route('superadmin.planes.create') }}" class="px-4 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition shadow-glow flex items-center gap-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            Nuevo plan
        </a>
    </div>

    @include('partials.flash')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($planes as $plan)
            <div class="rounded-3xl bg-white shadow-card p-6 flex flex-col {{ $plan->destacado ? 'ring-2 ring-iris-400' : '' }}">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-lg text-slate-800">{{ $plan->nombre }}</p>
                        <p class="text-xs text-slate-400">{{ $plan->descripcion }}</p>
                    </div>
                    @if ($plan->destacado)<span class="text-[10px] font-bold px-2 py-1 rounded-full bg-iris-100 text-iris-700">POPULAR</span>@endif
                </div>
                <p class="mt-4"><span class="text-3xl font-extrabold text-slate-800">Bs {{ number_format($plan->precio_mensual, 0) }}</span><span class="text-sm text-slate-400">/mes</span></p>

                <ul class="mt-4 space-y-2 text-sm text-slate-600 flex-1">
                    <li class="flex items-center gap-2"><span class="text-iris-500">●</span> Hasta {{ $plan->max_usuarios }} usuarios</li>
                    <li class="flex items-center gap-2"><span class="text-iris-500">●</span> Hasta {{ number_format($plan->max_pacientes) }} pacientes</li>
                    @foreach ($plan->caracteristicasLista() as $c)
                        <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> {{ $c }}</li>
                    @endforeach
                </ul>

                <div class="mt-5 flex items-center justify-between">
                    <span class="text-xs text-slate-400">{{ $plan->clinicas_count }} clinica(s) · {{ $plan->activo ? 'Activo' : 'Inactivo' }}</span>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('superadmin.planes.edit', $plan) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-iris-600 bg-iris-50 hover:bg-iris-100 transition">Editar</a>
                        <form method="POST" action="{{ route('superadmin.planes.destroy', $plan) }}" onsubmit="return confirm('Eliminar este plan?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-3xl bg-white shadow-card p-12 text-center text-slate-400">No hay planes creados.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $planes->links() }}</div>

@endsection
