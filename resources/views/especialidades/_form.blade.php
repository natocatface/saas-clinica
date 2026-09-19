@php
    $editing = $especialidad->exists;
@endphp
<form method="POST" action="{{ $editing ? route('especialidades.update', $especialidad) : route('especialidades.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
        <input type="text" name="nombre" value="{{ old('nombre', $especialidad->nombre) }}" required
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Descripcion</label>
        <textarea name="descripcion" rows="3"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">{{ old('descripcion', $especialidad->descripcion) }}</textarea>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tarifa ({{ moneda() }}) <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" name="tarifa" value="{{ old('tarifa', $especialidad->tarifa ?? 0) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Color</label>
            <input type="color" name="color" value="{{ old('color', $especialidad->color ?? '#7c44ff') }}"
                   class="w-full h-11 px-1.5 py-1 rounded-xl border border-slate-300 cursor-pointer">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
        <input type="checkbox" name="activo" value="1" {{ old('activo', $especialidad->activo ?? true) ? 'checked' : '' }}
               class="rounded border-slate-300 text-brand-600 focus:ring-brand-300">
        Especialidad activa
    </label>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Crear especialidad' }}
        </button>
        <a href="{{ route('especialidades.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
