@php $editing = $medico->exists; @endphp
<form method="POST" action="{{ $editing ? route('medicos.update', $medico) : route('medicos.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombres <span class="text-rose-500">*</span></label>
            <input type="text" name="nombres" value="{{ old('nombres', $medico->nombres) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Apellidos <span class="text-rose-500">*</span></label>
            <input type="text" name="apellidos" value="{{ old('apellidos', $medico->apellidos) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Especialidad</label>
            <select name="especialidad_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">— Seleccionar —</option>
                @foreach ($especialidades as $esp)
                    <option value="{{ $esp->id }}" {{ (string) old('especialidad_id', $medico->especialidad_id) === (string) $esp->id ? 'selected' : '' }}>{{ $esp->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Matricula / Registro</label>
            <input type="text" name="matricula" value="{{ old('matricula', $medico->matricula) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $medico->email) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $medico->telefono) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Hora inicio</label>
            <input type="time" name="hora_inicio" value="{{ old('hora_inicio', $medico->hora_inicio ? substr($medico->hora_inicio,0,5) : '') }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Hora fin</label>
            <input type="time" name="hora_fin" value="{{ old('hora_fin', $medico->hora_fin ? substr($medico->hora_fin,0,5) : '') }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
        <input type="checkbox" name="activo" value="1" {{ old('activo', $medico->activo ?? true) ? 'checked' : '' }}
               class="rounded border-slate-300 text-brand-600 focus:ring-brand-300">
        Medico activo
    </label>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Registrar medico' }}
        </button>
        <a href="{{ route('medicos.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
