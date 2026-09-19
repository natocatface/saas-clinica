@php $editing = $producto->exists; @endphp
<form method="POST" action="{{ $editing ? route('productos.update', $producto) : route('productos.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
            <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Codigo</label>
                <input type="text" name="codigo" value="{{ old('codigo', $producto->codigo) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Categoria</label>
                <input type="text" name="categoria" value="{{ old('categoria', $producto->categoria) }}" placeholder="Analgesico, Insumo..."
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Descripcion</label>
        <textarea name="descripcion" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">{{ old('descripcion', $producto->descripcion) }}</textarea>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Unidad</label>
            <input type="text" name="unidad" value="{{ old('unidad', $producto->unidad ?? 'unidad') }}" placeholder="caja, frasco..."
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Stock {{ $editing ? '' : 'inicial' }}</label>
            <input type="number" min="0" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}" {{ $editing ? 'readonly' : '' }}
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition {{ $editing ? 'bg-slate-50 text-slate-400' : '' }}">
            @if ($editing)<p class="text-[11px] text-slate-400 mt-1">Ajusta el stock con movimientos.</p>@endif
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Stock minimo</label>
            <input type="number" min="0" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? 0) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Vencimiento</label>
            <input type="date" name="vencimiento" value="{{ old('vencimiento', optional($producto->vencimiento)->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5 max-w-md">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio compra ({{ moneda() }})</label>
            <input type="number" step="0.01" min="0" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra ?? 0) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio venta ({{ moneda() }})</label>
            <input type="number" step="0.01" min="0" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta ?? 0) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
        <input type="checkbox" name="activo" value="1" {{ old('activo', $producto->activo ?? true) ? 'checked' : '' }}
               class="rounded border-slate-300 text-brand-600 focus:ring-brand-300">
        Producto activo
    </label>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Crear producto' }}
        </button>
        <a href="{{ route('productos.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
