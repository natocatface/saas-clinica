@php $editing = $usuario->exists; @endphp
<form method="POST" action="{{ $editing ? route('usuarios.update', $usuario) : route('usuarios.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre completo <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo <span class="text-rose-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Rol <span class="text-rose-500">*</span></label>
            <select name="role" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                @foreach ($roles as $k => $v)
                    <option value="{{ $k }}" {{ old('role', $usuario->role ?? 'recepcion') === $k ? 'selected' : '' }}>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefono</label>
            <input type="text" name="phone" value="{{ old('phone', $usuario->phone) }}"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Contrasena @if (!$editing)<span class="text-rose-500">*</span>@else<span class="text-slate-400 text-xs">(dejar vacio para no cambiar)</span>@endif
            </label>
            <input type="password" name="password" {{ $editing ? '' : 'required' }} autocomplete="new-password"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar contrasena</label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $usuario->is_active ?? true) ? 'checked' : '' }}
               class="rounded border-slate-300 text-brand-600 focus:ring-brand-300">
        Cuenta activa (puede iniciar sesion)
    </label>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
            {{ $editing ? 'Guardar cambios' : 'Crear usuario' }}
        </button>
        <a href="{{ route('usuarios.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
