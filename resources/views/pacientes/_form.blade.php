@php $editing = $paciente->exists; @endphp
<form method="POST" action="{{ $editing ? route('pacientes.update', $paciente) : route('pacientes.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombres <span class="text-rose-500">*</span></label>
            <input type="text" name="nombres" value="{{ old('nombres', $paciente->nombres) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Apellidos <span class="text-rose-500">*</span></label>
            <input type="text" name="apellidos" value="{{ old('apellidos', $paciente->apellidos) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">CI / Documento</label>
            <input type="text" name="ci" value="{{ old('ci', $paciente->ci) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha de nacimiento</label>
            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($paciente->fecha_nacimiento)->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Sexo</label>
            <select name="sexo" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">—</option>
                @foreach (['M' => 'Masculino', 'F' => 'Femenino', 'O' => 'Otro'] as $k => $v)
                    <option value="{{ $k }}" {{ old('sexo', $paciente->sexo) === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $paciente->telefono) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $paciente->email) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Grupo sanguineo</label>
            <select name="grupo_sanguineo" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">—</option>
                @foreach (['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $gs)
                    <option value="{{ $gs }}" {{ old('grupo_sanguineo', $paciente->grupo_sanguineo) === $gs ? 'selected' : '' }}>{{ $gs }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Direccion</label>
        <input type="text" name="direccion" value="{{ old('direccion', $paciente->direccion) }}"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Seguro</label>
            <input type="text" name="seguro" value="{{ old('seguro', $paciente->seguro) }}" placeholder="Particular, CNS, etc."
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Alergias</label>
            <input type="text" name="alergias" value="{{ old('alergias', $paciente->alergias) }}" placeholder="Ninguna conocida"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
        <input type="checkbox" name="activo" value="1" {{ old('activo', $paciente->activo ?? true) ? 'checked' : '' }}
               class="rounded border-slate-300 text-brand-600 focus:ring-brand-300">
        Paciente activo
    </label>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Registrar paciente' }}
        </button>
        <a href="{{ route('pacientes.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
