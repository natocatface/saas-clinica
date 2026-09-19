@php
    $editing = $factura->exists;
    $rows = old('items') ?: ($editing ? $factura->items->toArray() : [[]]);
@endphp
<form method="POST" action="{{ $editing ? route('facturas.update', $factura) : route('facturas.store') }}" class="space-y-6" id="factura-form">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Paciente <span class="text-rose-500">*</span></label>
            <select name="paciente_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                <option value="">— Seleccionar —</option>
                @foreach ($pacientes as $p)
                    <option value="{{ $p->id }}" {{ (string) old('paciente_id', $factura->paciente_id) === (string) $p->id ? 'selected' : '' }}>{{ $p->nombre_completo }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha <span class="text-rose-500">*</span></label>
            <input type="date" name="fecha" value="{{ old('fecha', optional($factura->fecha)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado <span class="text-rose-500">*</span></label>
            <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                @foreach ($estados as $k => $v)
                    <option value="{{ $k }}" {{ old('estado', $factura->estado ?? 'pendiente') === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Conceptos -->
    <div>
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-semibold text-slate-700">Conceptos / Servicios</p>
            <button type="button" id="add-item" class="text-sm font-medium text-brand-600 hover:underline flex items-center gap-1">
                @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Agregar
            </button>
        </div>

        <div id="items-wrap" class="space-y-3">
            @foreach ($rows as $i => $row)
                <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-center rounded-2xl bg-slate-50 border border-slate-100 p-3">
                    <div class="sm:col-span-6">
                        <input type="text" name="items[{{ $i }}][descripcion]" value="{{ $row['descripcion'] ?? '' }}" placeholder="Descripcion *"
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][cantidad]" value="{{ $row['cantidad'] ?? 1 }}" placeholder="Cant."
                               class="i-cant w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <input type="number" step="0.01" min="0" name="items[{{ $i }}][precio_unitario]" value="{{ $row['precio_unitario'] ?? 0 }}" placeholder="Precio"
                               class="i-precio w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-2">
                        <span class="i-sub flex-1 text-sm font-semibold text-slate-700 text-right">0.00</span>
                        <button type="button" class="remove-item shrink-0 w-9 h-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition grid place-items-center" title="Quitar">✕</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Metodo de pago</label>
            <select name="metodo_pago" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 outline-none transition">
                <option value="">—</option>
                @foreach (['efectivo' => 'Efectivo', 'tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'seguro' => 'Seguro'] as $k => $v)
                    <option value="{{ $k }}" {{ old('metodo_pago', $factura->metodo_pago) === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
            <label class="block text-sm font-medium text-slate-700 mb-1.5 mt-4">Notas</label>
            <textarea name="notas" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">{{ old('notas', $factura->notas) }}</textarea>
        </div>

        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-5 space-y-3 text-sm">
            <div class="flex justify-between text-slate-500"><span>Subtotal</span><span id="sum-subtotal" class="font-semibold text-slate-700">{{ moneda() }} 0.00</span></div>
            <div class="flex justify-between items-center text-slate-500">
                <span>Descuento</span>
                <div class="flex items-center gap-1">
                    <span>{{ moneda() }}</span>
                    <input type="number" step="0.01" min="0" name="descuento" id="descuento" value="{{ old('descuento', $factura->descuento ?? 0) }}"
                           class="w-24 px-2 py-1.5 rounded-lg border border-slate-300 text-sm text-right focus:border-brand-500 outline-none">
                </div>
            </div>
            <div class="flex justify-between border-t border-slate-200 pt-3 text-base">
                <span class="font-semibold text-slate-700">Total</span>
                <span id="sum-total" class="font-extrabold text-brand-600">{{ moneda() }} 0.00</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Crear factura' }}
        </button>
        <a href="{{ route('facturas.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>

<template id="item-template">
    <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 items-center rounded-2xl bg-slate-50 border border-slate-100 p-3">
        <div class="sm:col-span-6"><input type="text" name="items[__IDX__][descripcion]" placeholder="Descripcion *" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none"></div>
        <div class="sm:col-span-2"><input type="number" step="0.01" min="0" name="items[__IDX__][cantidad]" value="1" placeholder="Cant." class="i-cant w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none"></div>
        <div class="sm:col-span-2"><input type="number" step="0.01" min="0" name="items[__IDX__][precio_unitario]" value="0" placeholder="Precio" class="i-precio w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none"></div>
        <div class="sm:col-span-2 flex items-center gap-2">
            <span class="i-sub flex-1 text-sm font-semibold text-slate-700 text-right">0.00</span>
            <button type="button" class="remove-item shrink-0 w-9 h-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition grid place-items-center" title="Quitar">✕</button>
        </div>
    </div>
</template>

<script>
    (function () {
        let idx = {{ count($rows) }};
        const wrap = document.getElementById('items-wrap');
        const tpl = document.getElementById('item-template').innerHTML;
        const fmt = n => '{{ moneda() }} ' + (n || 0).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        function recalc() {
            let subtotal = 0;
            wrap.querySelectorAll('.item-row').forEach(row => {
                const c = parseFloat(row.querySelector('.i-cant')?.value) || 0;
                const p = parseFloat(row.querySelector('.i-precio')?.value) || 0;
                const s = c * p;
                subtotal += s;
                row.querySelector('.i-sub').textContent = s.toFixed(2);
            });
            const desc = parseFloat(document.getElementById('descuento').value) || 0;
            document.getElementById('sum-subtotal').textContent = fmt(subtotal);
            document.getElementById('sum-total').textContent = fmt(Math.max(0, subtotal - desc));
        }

        document.getElementById('add-item').addEventListener('click', function () {
            wrap.insertAdjacentHTML('beforeend', tpl.replace(/__IDX__/g, idx++));
            recalc();
        });

        document.getElementById('factura-form').addEventListener('input', function (e) {
            if (e.target.matches('.i-cant, .i-precio, #descuento')) recalc();
        });

        wrap.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item')) {
                const rows = wrap.querySelectorAll('.item-row');
                if (rows.length > 1) { e.target.closest('.item-row').remove(); recalc(); }
                else alert('Debe quedar al menos un concepto.');
            }
        });

        recalc();
    })();
</script>
