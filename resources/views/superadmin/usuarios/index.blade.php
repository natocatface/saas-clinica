@extends('layouts.superadmin')
@section('title', 'Usuarios globales')

@php
    $roleColor = ['super_admin'=>'bg-ink-900 text-acc-400','admin'=>'bg-iris-100 text-iris-700','medico'=>'bg-emerald-100 text-emerald-700','recepcion'=>'bg-sky-100 text-sky-700','enfermeria'=>'bg-amber-100 text-amber-700'];
@endphp

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Usuarios globales</h1>
            <p class="text-sm text-slate-500">{{ $usuarios->total() }} usuario(s) en la plataforma</p>
        </div>
        <a href="{{ route('superadmin.usuarios.create') }}" class="px-4 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition shadow-glow flex items-center gap-2">
            @include('partials.icon', ['name'=>'plus','class'=>'w-4 h-4']) Nuevo usuario
        </a>
    </div>

    @include('partials.flash')

    <div class="rounded-3xl bg-white shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px] max-w-sm">
                    <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">@include('partials.icon', ['name'=>'search','class'=>'w-5 h-5'])</span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Buscar usuario..." class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white focus:border-iris-400 outline-none transition">
                </div>
                <select name="clinica" onchange="this.form.submit()" class="px-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm focus:bg-white outline-none">
                    <option value="">Todas las clinicas</option>
                    @foreach ($clinicas as $c)<option value="{{ $c->id }}" {{ $clinicaId==$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach
                </select>
                <button class="px-4 py-2.5 rounded-full bg-iris-50 text-iris-600 text-sm font-medium hover:bg-iris-100 transition">Filtrar</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                    <th class="px-6 py-3 font-semibold">Usuario</th><th class="px-6 py-3 font-semibold">Clinica</th><th class="px-6 py-3 font-semibold">Rol</th><th class="px-6 py-3 font-semibold">Estado</th><th class="px-6 py-3 font-semibold text-right">Acciones</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($usuarios as $u)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-iris-100 text-iris-700 grid place-items-center text-xs font-bold">{{ $u->initials() }}</span>
                                    <div><p class="font-semibold text-slate-700">{{ $u->name }}</p><p class="text-xs text-slate-400">{{ $u->email }}</p></div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $u->clinica?->nombre ?? '— Plataforma —' }}</td>
                            <td class="px-6 py-3.5"><span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $roleColor[$u->role] ?? 'bg-slate-100 text-slate-600' }}">{{ $roles[$u->role] ?? ucfirst($u->role) }}</span></td>
                            <td class="px-6 py-3.5"><span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $u->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $u->is_active ? 'Activo' : 'Inactivo' }}</span></td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('superadmin.usuarios.edit', $u) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium text-iris-600 bg-iris-50 hover:bg-iris-100 transition">Editar</a>
                                    <form method="POST" action="{{ route('superadmin.usuarios.destroy', $u) }}" onsubmit="return confirm('Eliminar usuario?')">@csrf @method('DELETE')
                                        <button class="px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">No hay usuarios.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $usuarios->links() }}</div>
    </div>
@endsection
