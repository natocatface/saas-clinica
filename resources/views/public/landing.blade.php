<!DOCTYPE html>
<html lang="es" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SaaS Clinica · Software de gestion para clinicas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
            colors: { brand: {50:'#f3f1ff',100:'#ebe6ff',200:'#d9cfff',300:'#bda4ff',400:'#9a72ff',500:'#7c44ff',600:'#6d28f5',700:'#5d1ad1',800:'#4d18aa',900:'#3f1788'} }
        }}}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="h-full bg-white text-slate-700 antialiased">

<!-- Nav -->
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 grid place-items-center text-white font-bold">+</div>
            <span class="font-extrabold text-slate-800">SaaS Clinica</span>
        </div>
        <nav class="hidden sm:flex items-center gap-7 text-sm font-medium text-slate-600">
            <a href="#features" class="hover:text-brand-600">Funciones</a>
            <a href="#planes" class="hover:text-brand-600">Planes</a>
            <a href="{{ route('login') }}" class="hover:text-brand-600">Ingresar</a>
        </nav>
        <a href="{{ route('registro.show') }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">Prueba gratis</a>
    </div>
</header>

<!-- Hero -->
<section class="relative overflow-hidden">
    <div class="absolute -top-32 -right-24 w-[28rem] h-[28rem] bg-brand-200/40 rounded-full blur-3xl"></div>
    <div class="max-w-6xl mx-auto px-6 py-20 sm:py-28 relative grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-block text-xs font-semibold px-3 py-1.5 rounded-full bg-brand-50 text-brand-700 mb-5">Software medico todo-en-uno</span>
            <h1 class="text-4xl sm:text-5xl font-black leading-tight text-slate-900">Gestiona tu clinica <span class="text-brand-600">sin complicaciones</span></h1>
            <p class="mt-5 text-lg text-slate-500 max-w-lg">Citas, pacientes, historias clinicas, recetas, laboratorio, facturacion e inventario en una sola plataforma moderna y segura.</p>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('registro.show') }}" class="px-6 py-3.5 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">Crear mi clinica gratis</a>
                <a href="#planes" class="px-6 py-3.5 rounded-xl bg-white border border-slate-200 font-semibold text-slate-700 hover:border-brand-300 transition">Ver planes</a>
            </div>
            <p class="mt-4 text-sm text-slate-400">14 dias de prueba · Sin tarjeta de credito</p>
        </div>
        <div class="relative">
            <div class="rounded-3xl bg-gradient-to-br from-brand-600 to-brand-900 p-6 shadow-2xl">
                <div class="rounded-2xl bg-white/10 p-4 mb-4">
                    <div class="flex items-center justify-between text-white/90 text-sm mb-3"><span>Citas de hoy</span><span class="font-bold">28</span></div>
                    <div class="h-24 flex items-end gap-2">
                        @foreach ([40,65,50,80,60,95,75,100] as $h)
                            <div class="flex-1 rounded-t bg-white/30" style="height: {{ $h }}%"></div>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white/10 p-3 text-white"><p class="text-xl font-bold">1.3k</p><p class="text-[11px] text-white/70">Pacientes</p></div>
                    <div class="rounded-2xl bg-white/10 p-3 text-white"><p class="text-xl font-bold">Bs 86k</p><p class="text-[11px] text-white/70">Ingresos</p></div>
                    <div class="rounded-2xl bg-white/10 p-3 text-white"><p class="text-xl font-bold">99.9%</p><p class="text-[11px] text-white/70">Uptime</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section id="features" class="max-w-6xl mx-auto px-6 py-16">
    <div class="text-center max-w-2xl mx-auto mb-12">
        <h2 class="text-3xl font-extrabold text-slate-900">Todo lo que tu clinica necesita</h2>
        <p class="mt-3 text-slate-500">Una suite completa para digitalizar y hacer crecer tu centro medico.</p>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $features = [
                ['Agenda y citas', 'Programa, confirma y da seguimiento a las citas con tu equipo medico.'],
                ['Pacientes', 'Fichas completas con historial, contacto, seguro y documentos.'],
                ['Historias clinicas', 'Expediente electronico con signos vitales, diagnosticos y evolucion.'],
                ['Recetas y laboratorio', 'Emite recetas y ordenes de examen, imprimibles al instante.'],
                ['Facturacion', 'Cobros, descuentos, estados de pago y reportes de ingresos.'],
                ['Farmacia e inventario', 'Control de stock, alertas de minimos y movimientos.'],
            ];
        @endphp
        @foreach ($features as $f)
            <div class="rounded-3xl border border-slate-100 p-6 hover:shadow-xl hover:shadow-brand-500/5 transition">
                <div class="w-11 h-11 rounded-2xl bg-brand-50 text-brand-600 grid place-items-center mb-4 font-bold">✓</div>
                <h3 class="font-bold text-slate-800">{{ $f[0] }}</h3>
                <p class="mt-1.5 text-sm text-slate-500">{{ $f[1] }}</p>
            </div>
        @endforeach
    </div>
</section>

<!-- Planes -->
<section id="planes" class="bg-slate-50 py-20">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-extrabold text-slate-900">Planes para cada etapa</h2>
            <p class="mt-3 text-slate-500">Empieza gratis 14 dias. Cambia o cancela cuando quieras.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 items-start">
            @forelse ($planes as $plan)
                <div class="rounded-3xl bg-white p-7 shadow-card border {{ $plan->destacado ? 'border-brand-400 ring-2 ring-brand-200 relative' : 'border-slate-100' }}">
                    @if ($plan->destacado)<span class="absolute -top-3 left-1/2 -translate-x-1/2 text-[11px] font-bold px-3 py-1 rounded-full bg-brand-600 text-white">MAS POPULAR</span>@endif
                    <h3 class="font-bold text-lg text-slate-800">{{ $plan->nombre }}</h3>
                    <p class="text-sm text-slate-400">{{ $plan->descripcion }}</p>
                    <p class="mt-4"><span class="text-4xl font-black text-slate-900">Bs {{ number_format($plan->precio_mensual, 0) }}</span><span class="text-slate-400">/mes</span></p>
                    <ul class="mt-5 space-y-2.5 text-sm text-slate-600">
                        <li class="flex items-center gap-2"><span class="text-brand-500">●</span> Hasta {{ $plan->max_usuarios }} usuarios</li>
                        <li class="flex items-center gap-2"><span class="text-brand-500">●</span> Hasta {{ number_format($plan->max_pacientes) }} pacientes</li>
                        @foreach ($plan->caracteristicasLista() as $c)
                            <li class="flex items-center gap-2"><span class="text-emerald-500">✓</span> {{ $c }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('registro.show', ['plan' => $plan->id]) }}" class="mt-6 block text-center px-4 py-3 rounded-xl font-semibold transition {{ $plan->destacado ? 'bg-brand-600 text-white hover:bg-brand-700 shadow-lg shadow-brand-500/30' : 'bg-brand-50 text-brand-700 hover:bg-brand-100' }}">Elegir {{ $plan->nombre }}</a>
                </div>
            @empty
                <p class="col-span-full text-center text-slate-400">Pronto publicaremos nuestros planes.</p>
            @endforelse
        </div>
    </div>
</section>

<!-- CTA -->
<section class="max-w-6xl mx-auto px-6 py-20">
    <div class="rounded-3xl bg-gradient-to-br from-brand-600 to-brand-900 p-10 sm:p-14 text-center text-white relative overflow-hidden">
        <div class="absolute -top-20 -right-10 w-72 h-72 bg-white/10 rounded-full blur-2xl"></div>
        <h2 class="relative text-3xl font-extrabold">Digitaliza tu clinica hoy</h2>
        <p class="relative mt-3 text-brand-100 max-w-xl mx-auto">Crea tu cuenta en minutos y empieza a gestionar pacientes y citas sin papeles.</p>
        <a href="{{ route('registro.show') }}" class="relative inline-block mt-7 px-7 py-3.5 rounded-xl bg-white text-brand-700 font-semibold hover:bg-brand-50 transition">Comenzar prueba gratis</a>
    </div>
</section>

<footer class="border-t border-slate-100">
    <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-400">
        <p>© {{ date('Y') }} SaaS Clinica. Todos los derechos reservados.</p>
        <div class="flex items-center gap-5">
            <a href="{{ route('login') }}" class="hover:text-brand-600">Ingresar</a>
            <a href="{{ route('registro.show') }}" class="hover:text-brand-600">Crear cuenta</a>
        </div>
    </div>
</footer>

</body>
</html>
