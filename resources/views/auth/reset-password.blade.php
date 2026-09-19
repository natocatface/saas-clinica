<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer contrasena · {{ config('app.name') }}</title>
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
    <div class="w-full max-w-md">
        <div class="flex items-center gap-2.5 text-brand-700 mb-6 justify-center">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 grid place-items-center text-white font-bold">+</div>
            <span class="font-extrabold text-lg">{{ config('app.name') }}</span>
        </div>

        <div class="rounded-3xl bg-white shadow-card p-6 sm:p-8">
            <h1 class="text-xl font-extrabold text-slate-800">Nueva contrasena</h1>
            <p class="mt-1 text-sm text-slate-500">Define tu nueva contrasena para acceder.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nueva contrasena</label>
                    <input type="password" name="password" required autocomplete="new-password"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirmar contrasena</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-brand-600 to-brand-500 text-white font-semibold hover:from-brand-700 hover:to-brand-600 transition shadow-lg shadow-brand-500/30">
                    Restablecer contrasena
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
