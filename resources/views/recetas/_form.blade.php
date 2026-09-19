@php
    $editing = $receta->exists;
    $rows = old('items') ?: ($editing ? $receta->items->toArray() : [[]]);
@endphp
<form method="POST" action="{{ $editing ? route('recetas.update', $receta) : route('recetas.store') }}" class="space-y-6">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Paciente <span class="text-rose-500">*</span></label>
            <select name="paciente_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">— Seleccionar —</option>
                @foreach ($pacientes as $p)
                    <option value="{{ $p->id }}" {{ (string) old('paciente_id', $receta->paciente_id) === (string) $p->id ? 'selected' : '' }}>{{ $p->nombre_completo }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Medico</label>
            <select name="medico_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">— Seleccionar —</option>
                @foreach ($medicos as $m)
                    <option value="{{ $m->id }}" {{ (string) old('medico_id', $receta->medico_id) === (string) $m->id ? 'selected' : '' }}>Dr(a). {{ $m->nombre_completo }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha <span class="text-rose-500">*</span></label>
            <input type="date" name="fecha" value="{{ old('fecha', optional($receta->fecha)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Diagnostico</label>
        <input type="text" name="diagnostico" value="{{ old('diagnostico', $receta->diagnostico) }}"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
    </div>

    <!-- Medicamentos -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-semibold text-slate-700">Medicamentos</p>
            <button type="button" id="add-item" class="text-sm font-medium text-brand-600 hover:underline flex items-center gap-1">
                @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Agregar
            </button>
        </div>

        <div id="items-wrap" class="space-y-3">
            @foreach ($rows as $i => $row)
                <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-start rounded-2xl bg-slate-50 border border-slate-100 p-3">
                    <div class="sm:col-span-4">
                        <input type="text" name="items[{{ $i }}][medicamento]" value="{{ $row['medicamento'] ?? '' }}" placeholder="Medicamento *"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <input type="text" name="items[{{ $i }}][dosis]" value="{{ $row['dosis'] ?? '' }}" placeholder="Dosis"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <input type="text" name="items[{{ $i }}][frecuencia]" value="{{ $row['frecuencia'] ?? '' }}" placeholder="Frecuencia"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <input type="text" name="items[{{ $i }}][duracion]" value="{{ $row['duracion'] ?? '' }}" placeholder="Duracion"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    </div>
                    <div class="sm:col-span-2 flex gap-2">
                        <input type="text" name="items[{{ $i }}][indicaciones]" value="{{ $row['indicaciones'] ?? '' }}" placeholder="Indicaciones"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                        <button type="button" class="remove-item shrink-0 w-9 h-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition grid place-items-center" title="Quitar">✕</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas / Recomendaciones</label>
        <textarea name="notas" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">{{ old('notas', $receta->notas) }}</textarea>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Emitir receta' }}
        </button>
        <a href="{{ route('recetas.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>

<template id="item-template">
    <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-start rounded-2xl bg-slate-50 border border-slate-100 p-3">
        <div class="sm:col-span-4"><input type="text" name="items[__IDX__][medicamento]" placeholder="Medicamento *" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none"></div>
        <div class="sm:col-span-2"><input type="text" name="items[__IDX__][dosis]" placeholder="Dosis" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none"></div>
        <div class="sm:col-span-2"><input type="text" name="items[__IDX__][frecuencia]" placeholder="Frecuencia" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none"></div>
        <div class="sm:col-span-2"><input type="text" name="items[__IDX__][duracion]" placeholder="Duracion" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none"></div>
        <div class="sm:col-span-2 flex gap-2">
            <input type="text" name="items[__IDX__][indicaciones]" placeholder="Indicaciones" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
            <button type="button" class="remove-item shrink-0 w-9 h-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition grid place-items-center" title="Quitar">✕</button>
        </div>
    </div>
</template>

<script>
    (function () {
        let idx = {{ count($rows) }};
        const wrap = document.getElementById('items-wrap');
        const tpl = document.getElementById('item-template').innerHTML;

        document.getElementById('add-item').addEventListener('click', function () {
            wrap.insertAdjacentHTML('beforeend', tpl.replace(/__IDX__/g, idx++));
        });

        wrap.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                const rows = wrap.querySelectorAll('.item-row');
                if (rows.length > 1) e.target.closest('.item-row').remove();
                else alert('Debe quedar al menos un medicamento.');
            }
        });
    })();
</script>
