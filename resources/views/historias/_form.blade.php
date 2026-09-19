@php $editing = $historia->exists; @endphp
<form method="POST" action="{{ $editing ? route('historias.update', $historia) : route('historias.store') }}" class="space-y-6">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Paciente <span class="text-rose-500">*</span></label>
            <select name="paciente_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">— Seleccionar —</option>
                @foreach ($pacientes as $p)
                    <option value="{{ $p->id }}" {{ (string) old('paciente_id', $historia->paciente_id) === (string) $p->id ? 'selected' : '' }}>{{ $p->nombre_completo }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Medico</label>
            <select name="medico_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">— Seleccionar —</option>
                @foreach ($medicos as $m)
                    <option value="{{ $m->id }}" {{ (string) old('medico_id', $historia->medico_id) === (string) $m->id ? 'selected' : '' }}>Dr(a). {{ $m->nombre_completo }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha <span class="text-rose-500">*</span></label>
            <input type="date" name="fecha" value="{{ old('fecha', optional($historia->fecha)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Motivo de consulta</label>
        <input type="text" name="motivo_consulta" value="{{ old('motivo_consulta', $historia->motivo_consulta) }}"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
    </div>

    <!-- Signos vitales -->
    <div>
        <p class="text-sm font-semibold text-slate-700 mb-3">Signos vitales</p>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Peso (kg)</label>
                <input type="number" step="0.1" name="peso" value="{{ old('peso', $historia->peso) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Talla (cm)</label>
                <input type="number" step="0.1" name="talla" value="{{ old('talla', $historia->talla) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">P. arterial</label>
                <input type="text" name="presion_arterial" value="{{ old('presion_arterial', $historia->presion_arterial) }}" placeholder="120/80" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Temp. (C)</label>
                <input type="number" step="0.1" name="temperatura" value="{{ old('temperatura', $historia->temperatura) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">F. cardiaca</label>
                <input type="number" name="frecuencia_cardiaca" value="{{ old('frecuencia_cardiaca', $historia->frecuencia_cardiaca) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
            </div>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Sintomas</label>
            <textarea name="sintomas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">{{ old('sintomas', $historia->sintomas) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Diagnostico</label>
            <textarea name="diagnostico" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">{{ old('diagnostico', $historia->diagnostico) }}</textarea>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tratamiento</label>
            <textarea name="tratamiento" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">{{ old('tratamiento', $historia->tratamiento) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Observaciones</label>
            <textarea name="observaciones" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">{{ old('observaciones', $historia->observaciones) }}</textarea>
        </div>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Registrar atencion' }}
        </button>
        <a href="{{ route('historias.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
