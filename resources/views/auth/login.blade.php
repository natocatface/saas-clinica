<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesion · {{ config('app.name') }}</title>
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
<div class="min-h-full flex">

    <!-- Panel izquierdo (marca) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 text-white p-12 flex-col justify-between">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-32 -left-16 w-96 h-96 bg-brand-400/30 rounded-full blur-3xl"></div>

        <div class="relative flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-white/15 grid place-items-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <span class="text-xl font-bold">{{ config('app.name') }}</span>
        </div>

        <div class="relative">
            <h1 class="text-4xl font-extrabold leading-tight">Tu clinica,<br>bajo control total.</h1>
            <p class="mt-4 text-brand-100 max-w-md">Gestiona citas, pacientes, historias clinicas, facturacion e inventario desde un solo lugar, con un panel moderno y en tiempo real.</p>

            <div class="mt-8 grid grid-cols-3 gap-4 max-w-md">
                <div class="bg-white/10 rounded-2xl p-4 backdrop-blur">
                    <p class="text-2xl font-bold">+1.3k</p>
                    <p class="text-xs text-brand-200">Pacientes</p>
                </div>
                <div class="bg-white/10 rounded-2xl p-4 backdrop-blur">
                    <p class="text-2xl font-bold">28</p>
                    <p class="text-xs text-brand-200">Citas hoy</p>
                </div>
                <div class="bg-white/10 rounded-2xl p-4 backdrop-blur">
                    <p class="text-2xl font-bold">99.9%</p>
                    <p class="text-xs text-brand-200">Uptime</p>
                </div>
            </div>
        </div>

        <p class="relative text-xs text-brand-200">© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
    </div>

    <!-- Panel derecho (formulario) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            <div class="lg:hidden flex items-center gap-3 mb-8 text-brand-700">
                <div class="w-11 h-11 rounded-xl bg-brand-600 text-white grid place-items-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span class="text-xl font-bold">{{ config('app.name') }}</span>
            </div>

            <h2 class="text-2xl font-extrabold text-slate-800">Bienvenido de nuevo</h2>
            <p class="mt-1 text-sm text-slate-500">Ingresa tus credenciales para acceder al panel.</p>

            @if ($errors->any())
                <div class="mt-6 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo electronico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="tucorreo@clinica.test"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm
                                  focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Contrasena</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm
                                  focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                </div>
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-300">
                        Recordarme
                    </label>
                    <a href="{{ route('password.request') }}" class="text-brand-600 font-medium hover:underline">Olvidaste tu contrasena?</a>
                </div>
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-semibold
                               shadow-lg shadow-brand-500/30 hover:from-brand-700 hover:to-brand-600 transition">
                    Iniciar sesion
                </button>
            </form>

            <div class="mt-8 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-xs text-slate-500">
                <p class="font-semibold text-slate-600 mb-1">Cuentas de prueba (seeder):</p>
                admin@clinica.test · medico@clinica.test · recepcion@clinica.test<br>
                Contrasena: <span class="font-mono">password</span>
            </div>
        </div>
    </div>
</div>
</body>
</html>
