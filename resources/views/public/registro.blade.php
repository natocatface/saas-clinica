<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta · SaaS Clinica</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
            colors: { brand: {50:'#f3f1ff',100:'#ebe6ff',200:'#d9cfff',300:'#bda4ff',400:'#9a72ff',500:'#7c44ff',600:'#6d28f5',700:'#5d1ad1',800:'#4d18aa',900:'#3f1788'} }
        }}}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="h-full bg-slate-100">
<div class="min-h-full flex items-center justify-center p-6">
    <div class="w-full max-w-3xl">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 text-brand-700">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 grid place-items-center text-white font-bold">+</div>
                <span class="font-extrabold">SaaS Clinica</span>
            </a>
            <a href="{{ route('login') }}" class="text-sm font-medium text-brand-600 hover:underline">Ya tengo cuenta</a>
        </div>

        <div class="rounded-3xl bg-white shadow-card p-6 sm:p-9">
            <h1 class="text-2xl font-extrabold text-slate-800">Crea tu clinica</h1>
            <p class="mt-1 text-sm text-slate-500">14 dias de prueba gratis. Configura tu cuenta en un minuto.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
                    <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('registro.store') }}" class="mt-6 space-y-6">
                @csrf

                <div>
                    <p class="text-sm font-semibold text-slate-700 mb-3">Datos de la clinica</p>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="clinica_nombre" value="{{ old('clinica_nombre') }}" required placeholder="Nombre de la clinica *"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                        <input type="text" name="ciudad" value="{{ old('ciudad') }}" placeholder="Ciudad"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-700 mb-3">Elige un plan</p>
                    <div class="grid sm:grid-cols-3 gap-3">
                        @foreach ($planes as $plan)
                            <label class="cursor-pointer">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" class="peer sr-only" {{ (string) old('plan_id', $planSeleccionado) === (string) $plan->id ? 'checked' : '' }}>
                                <div class="rounded-2xl border border-slate-200 p-4 peer-checked:border-brand-500 peer-checked:ring-2 peer-checked:ring-brand-100 transition">
                                    <p class="font-bold text-slate-800">{{ $plan->nombre }}</p>
                                    <p class="text-lg font-extrabold text-brand-600">Bs {{ number_format($plan->precio_mensual, 0) }}<span class="text-xs font-normal text-slate-400">/mes</span></p>
                                    <p class="text-[11px] text-slate-400 mt-1">{{ $plan->max_usuarios }} usuarios · {{ number_format($plan->max_pacientes) }} pacientes</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-700 mb-3">Tu cuenta de administrador</p>
                    <div class="space-y-4">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <input type="text" name="admin_name" value="{{ old('admin_name') }}" required placeholder="Tu nombre *"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                            <input type="email" name="admin_email" value="{{ old('admin_email') }}" required placeholder="Tu correo *"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <input type="password" name="admin_password" required placeholder="Contrasena *"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                            <input type="password" name="admin_password_confirmation" required placeholder="Confirmar contrasena *"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-semibold hover:from-brand-700 hover:to-brand-600 transition shadow-lg shadow-brand-500/30">
                    Crear mi clinica
                </button>
                <p class="text-center text-xs text-slate-400">Al registrarte aceptas los terminos del servicio.</p>
            </form>
        </div>
    </div>
</div>
</body>
</html>
