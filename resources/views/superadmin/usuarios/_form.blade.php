@php $editing = $usuario->exists; @endphp
<form method="POST" action="{{ $editing ? route('superadmin.usuarios.update', $usuario) : route('superadmin.usuarios.store') }}" class="space-y-5">
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre completo <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo <span class="text-rose-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Rol <span class="text-rose-500">*</span></label>
            <select name="role" id="u-role" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none transition">
                @foreach ($roles as $k=>$v)<option value="{{ $k }}" {{ old('role',$usuario->role??'admin')===$k?'selected':'' }}>{{ $v }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Clinica</label>
            <select name="clinica_id" id="u-clinica" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-iris-500 outline-none transition">
                <option value="">— Ninguna (plataforma) —</option>
                @foreach ($clinicas as $c)<option value="{{ $c->id }}" {{ (string)old('clinica_id',$usuario->clinica_id)===(string)$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach
            </select>
            <p class="text-[11px] text-slate-400 mt-1">El Super Admin no pertenece a ninguna clinica.</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefono</label>
            <input type="text" name="phone" value="{{ old('phone', $usuario->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Contrasena @if (!$editing)<span class="text-rose-500">*</span>@else<span class="text-slate-400 text-xs">(opcional)</span>@endif</label>
            <input type="password" name="password" {{ $editing ? '' : 'required' }} autocomplete="new-password" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar contrasena</label>
            <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-iris-500 outline-none transition">
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $usuario->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-iris-600 focus:ring-iris-300"> Cuenta activa
    </label>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-iris-600 text-white text-sm font-semibold hover:bg-iris-500 transition shadow-glow">{{ $editing ? 'Guardar cambios' : 'Crear usuario' }}</button>
        <a href="{{ route('superadmin.usuarios.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Cancelar</a>
    </div>
</form>
<script>
    (function(){
        const role=document.getElementById('u-role'), cli=document.getElementById('u-clinica');
        function sync(){ const sa=role.value==='super_admin'; cli.disabled=sa; if(sa) cli.value=''; cli.classList.toggle('bg-slate-100',sa); cli.classList.toggle('text-slate-400',sa); }
        role.addEventListener('change',sync); sync();
    })();
</script>
