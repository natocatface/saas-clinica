@php $editing = $suscripcion->exists; @endphp
<form method="POST" action="{{ $editing ? route('superadmin.suscripciones.update', $suscripcion) : route('superadmin.suscripciones.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Clinica <span class="text-rose-500">*</span></label>
            <select name="clinica_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none transition">
                <option value="">— Seleccionar —</option>
                @foreach ($clinicas as $c)<option value="{{ $c->id }}" {{ (string)old('clinica_id',$suscripcion->clinica_id)===(string)$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Plan</label>
            <select name="plan_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none transition">
                <option value="">— Sin plan —</option>
                @foreach ($planes as $p)<option value="{{ $p->id }}" data-precio="{{ $p->precio_mensual }}" {{ (string)old('plan_id',$suscripcion->plan_id)===(string)$p->id?'selected':'' }}>{{ $p->nombre }} (Bs {{ number_format($p->precio_mensual,0) }})</option>@endforeach
            </select>
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Monto (Bs) <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" name="monto" id="sus-monto" value="{{ old('monto', $suscripcion->monto ?? 0) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Ciclo <span class="text-rose-500">*</span></label>
            <select name="ciclo" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none transition">
                @foreach (['mensual'=>'Mensual','anual'=>'Anual'] as $k=>$v)<option value="{{ $k }}" {{ old('ciclo',$suscripcion->ciclo??'mensual')===$k?'selected':'' }}>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado <span class="text-rose-500">*</span></label>
            <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none transition">
                @foreach ($estados as $k=>$v)<option value="{{ $k }}" {{ old('estado',$suscripcion->estado??'activa')===$k?'selected':'' }}>{{ $v }}</option>@endforeach
            </select>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Inicio <span class="text-rose-500">*</span></label>
            <input type="date" name="inicio" value="{{ old('inicio', optional($suscripcion->inicio)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Fin</label>
            <input type="date" name="fin" value="{{ old('fin', optional($suscripcion->fin)->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition shadow-glow">{{ $editing ? 'Guardar cambios' : 'Crear suscripcion' }}</button>
        <a href="{{ route('superadmin.suscripciones.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
<script>
    (function(){
        const plan=document.querySelector('select[name="plan_id"]'), monto=document.getElementById('sus-monto');
        plan?.addEventListener('change',function(){ const p=plan.options[plan.selectedIndex]?.getAttribute('data-precio'); if(p) monto.value=p; });
    })();
</script>
