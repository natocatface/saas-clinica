@php $editing = $plan->exists; @endphp
<form method="POST" action="{{ $editing ? route('superadmin.planes.update', $plan) : route('superadmin.planes.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
            <input type="text" name="nombre" value="{{ old('nombre', $plan->nombre) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio mensual (Bs) <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" name="precio_mensual" value="{{ old('precio_mensual', $plan->precio_mensual ?? 0) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Descripcion</label>
        <input type="text" name="descripcion" value="{{ old('descripcion', $plan->descripcion) }}"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Maximo de usuarios <span class="text-rose-500">*</span></label>
            <input type="number" min="1" name="max_usuarios" value="{{ old('max_usuarios', $plan->max_usuarios ?? 5) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Maximo de pacientes <span class="text-rose-500">*</span></label>
            <input type="number" min="1" name="max_pacientes" value="{{ old('max_pacientes', $plan->max_pacientes ?? 500) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Caracteristicas <span class="text-slate-400 text-xs">(una por linea)</span></label>
        <textarea name="caracteristicas" rows="4" placeholder="Soporte prioritario&#10;Reportes avanzados&#10;Respaldos diarios"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">{{ old('caracteristicas', $plan->caracteristicas) }}</textarea>
    </div>

    <div class="flex items-center gap-6">
        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
            <input type="checkbox" name="destacado" value="1" {{ old('destacado', $plan->destacado ?? false) ? 'checked' : '' }} class="rounded border-slate-300 text-iris-600 focus:ring-iris-300"> Destacado (popular)
        </label>
        <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
            <input type="checkbox" name="activo" value="1" {{ old('activo', $plan->activo ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-iris-600 focus:ring-iris-300"> Activo
        </label>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition shadow-glow">{{ $editing ? 'Guardar cambios' : 'Crear plan' }}</button>
        <a href="{{ route('superadmin.planes.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
