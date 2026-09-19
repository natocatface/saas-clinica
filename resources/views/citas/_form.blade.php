@php $editing = $cita->exists; @endphp
<form method="POST" action="{{ $editing ? route('citas.update', $cita) : route('citas.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Paciente <span class="text-rose-500">*</span></label>
        <select name="paciente_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
            <option value="">— Seleccionar paciente —</option>
            @foreach ($pacientes as $p)
                <option value="{{ $p->id }}" {{ (string) old('paciente_id', $cita->paciente_id) === (string) $p->id ? 'selected' : '' }}>{{ $p->nombre_completo }} {{ $p->ci ? '('.$p->ci.')' : '' }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Medico <span class="text-rose-500">*</span></label>
            <select name="medico_id" id="medico_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">— Seleccionar medico —</option>
                @foreach ($medicos as $m)
                    <option value="{{ $m->id }}" data-esp="{{ $m->especialidad_id }}" {{ (string) old('medico_id', $cita->medico_id) === (string) $m->id ? 'selected' : '' }}>Dr(a). {{ $m->nombre_completo }} — {{ $m->especialidad?->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Especialidad</label>
            <select name="especialidad_id" id="especialidad_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">— Seleccionar —</option>
                @foreach ($especialidades as $e)
                    <option value="{{ $e->id }}" {{ (string) old('especialidad_id', $cita->especialidad_id) === (string) $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha <span class="text-rose-500">*</span></label>
            <input type="date" name="fecha" value="{{ old('fecha', optional($cita->fecha)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Hora <span class="text-rose-500">*</span></label>
            <input type="time" name="hora" value="{{ old('hora', $cita->hora ? substr($cita->hora,0,5) : '08:00') }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Motivo</label>
        <input type="text" name="motivo" value="{{ old('motivo', $cita->motivo) }}" placeholder="Motivo de la consulta"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado <span class="text-rose-500">*</span></label>
        <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
            @foreach ($estados as $k => $v)
                <option value="{{ $k }}" {{ old('estado', $cita->estado ?? 'programada') === $k ? 'selected' : '' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas</label>
        <textarea name="notas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">{{ old('notas', $cita->notas) }}</textarea>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Registrar cita' }}
        </button>
        <a href="{{ route('citas.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>

<script>
    // Autocompletar especialidad segun el medico seleccionado
    (function () {
        const med = document.getElementById('medico_id');
        const esp = document.getElementById('especialidad_id');
        med?.addEventListener('change', function () {
            const opt = med.options[med.selectedIndex];
            const espId = opt?.getAttribute('data-esp');
            if (espId) esp.value = espId;
        });
    })();
</script>
