@php $editing = $clinica->exists; @endphp
<form method="POST" action="{{ $editing ? route('superadmin.clinicas.update', $clinica) : route('superadmin.clinicas.store') }}" class="space-y-6">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre de la clinica <span class="text-rose-500">*</span></label>
            <input type="text" name="nombre" value="{{ old('nombre', $clinica->nombre) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 focus:ring-2 focus:ring-iris-100 outline-none transition">
        </div>
        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">NIT</label>
                <input type="text" name="nit" value="{{ old('nit', $clinica->nit) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Color</label>
                <input type="color" name="color" value="{{ old('color', $clinica->color ?? '#7c44ff') }}"
                       class="w-full h-11 px-1.5 py-1 rounded-xl border border-slate-300 cursor-pointer">
            </div>
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $clinica->email) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $clinica->telefono) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Ciudad</label>
            <input type="text" name="ciudad" value="{{ old('ciudad', $clinica->ciudad) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Direccion</label>
        <input type="text" name="direccion" value="{{ old('direccion', $clinica->direccion) }}"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Plan</label>
            <select name="plan_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none transition">
                <option value="">— Sin plan —</option>
                @foreach ($planes as $plan)
                    <option value="{{ $plan->id }}" {{ (string) old('plan_id', $clinica->plan_id) === (string) $plan->id ? 'selected' : '' }}>{{ $plan->nombre }} (Bs {{ number_format($plan->precio_mensual, 0) }}/mes)</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado <span class="text-rose-500">*</span></label>
            <select name="estado" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none transition">
                @foreach (\App\Models\Clinica::ESTADOS as $k => $v)
                    <option value="{{ $k }}" {{ old('estado', $clinica->estado ?? 'prueba') === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @unless ($editing)
        <div class="rounded-2xl bg-iris-50 border border-iris-100 p-5">
            <p class="text-sm font-semibold text-iris-700 mb-1">Administrador de la clinica</p>
            <p class="text-xs text-iris-600/80 mb-4">Se crea la cuenta inicial con la que el administrador accedera al sistema de su clinica.</p>
            <div class="grid sm:grid-cols-3 gap-4">
                <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="Nombre del admin *" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
                <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="Correo del admin *" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
                <input type="password" name="admin_password" placeholder="Contrasena *" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
            </div>
        </div>
    @endunless

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition shadow-glow">
            {{ $editing ? 'Guardar cambios' : 'Crear clinica' }}
        </button>
        <a href="{{ route('superadmin.clinicas.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
