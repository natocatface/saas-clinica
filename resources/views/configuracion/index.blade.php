@extends('layouts.app')
@section('title', 'Configuracion')

@section('content')
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
            @include('partials.icon', ['name' => 'cog', 'class' => 'w-6 h-6'])
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Configuracion</h1>
            <p class="text-sm text-slate-500">Datos generales de la clinica</p>
        </div>
    </div>

    @include('partials.flash')

    <div class="max-w-3xl rounded-3xl bg-white shadow-card p-6 sm:p-8">
        <form method="POST" action="{{ route('configuracion.update') }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre de la clinica</label>
                    <input type="text" name="clinica_nombre" value="{{ old('clinica_nombre', $config['clinica_nombre']) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NIT</label>
                    <input type="text" name="clinica_nit" value="{{ old('clinica_nit', $config['clinica_nit']) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Direccion</label>
                <input type="text" name="clinica_direccion" value="{{ old('clinica_direccion', $config['clinica_direccion']) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
            </div>

            <div class="grid sm:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ciudad</label>
                    <input type="text" name="clinica_ciudad" value="{{ old('clinica_ciudad', $config['clinica_ciudad']) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefono</label>
                    <input type="text" name="clinica_telefono" value="{{ old('clinica_telefono', $config['clinica_telefono']) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Moneda</label>
                    <input type="text" name="moneda" value="{{ old('moneda', $config['moneda'] ?? 'Bs') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input type="email" name="clinica_email" value="{{ old('clinica_email', $config['clinica_email']) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Mensaje de pie (recetas/facturas)</label>
                <input type="text" name="mensaje_pie" value="{{ old('mensaje_pie', $config['mensaje_pie']) }}" placeholder="Gracias por su preferencia"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">Guardar configuracion</button>
            </div>
        </form>
    </div>
@endsection
