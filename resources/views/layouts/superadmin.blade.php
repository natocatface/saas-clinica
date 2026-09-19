<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · Super Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
            colors: {
                ink: {900:'#0b1020',800:'#0f1629',700:'#16203b',600:'#1e2a4a'},
                acc: {400:'#22d3ee',500:'#06b6d4',600:'#0891b2'},
                iris:{400:'#818cf8',500:'#6366f1',600:'#4f46e5'}
            },
            boxShadow: { glow: '0 10px 40px -10px rgba(99,102,241,0.45)' }
        }}}
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        body{font-family:'Inter',sans-serif}
        ::-webkit-scrollbar{width:8px;height:8px}
        ::-webkit-scrollbar-thumb{background:#334155;border-radius:99px}
        ::-webkit-scrollbar-track{background:transparent}
        .sa-link.active{background:linear-gradient(90deg,rgba(99,102,241,.25),rgba(34,211,238,.12));color:#fff}
        .sa-link.active .ico{color:#22d3ee}
    </style>
    @stack('head')
</head>
<body class="h-full bg-slate-100 text-slate-700 antialiased">
<div class="min-h-full flex">

    <!-- Sidebar -->
    <aside id="sa-sidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full lg:translate-x-0 transition-transform duration-300
              bg-ink-900 text-slate-300 flex flex-col">
        <div class="h-16 flex items-center gap-3 px-6 border-b border-white/5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-iris-500 to-acc-500 grid place-items-center text-white font-bold">S</div>
            <div class="leading-tight">
                <p class="text-white font-bold">Super Admin</p>
                <p class="text-[11px] text-acc-400">Plataforma SaaS</p>
            </div>
        </div>

        @php
            $items = [
                ['route' => 'superadmin.dashboard', 'pattern' => 'superadmin.dashboard*', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                ['route' => 'superadmin.clinicas.index', 'pattern' => 'superadmin.clinicas.*', 'label' => 'Clinicas', 'icon' => 'box'],
                ['route' => 'superadmin.planes.index', 'pattern' => 'superadmin.planes.*', 'label' => 'Planes', 'icon' => 'grid'],
                ['route' => 'superadmin.suscripciones.index', 'pattern' => 'superadmin.suscripciones.*', 'label' => 'Suscripciones', 'icon' => 'cash'],
                ['route' => 'superadmin.usuarios.index', 'pattern' => 'superadmin.usuarios.*', 'label' => 'Usuarios', 'icon' => 'users'],
                ['route' => 'superadmin.reportes.index', 'pattern' => 'superadmin.reportes.*', 'label' => 'Reportes', 'icon' => 'chart'],
            ];
        @endphp

        <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-1">
            <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Plataforma</p>
            @foreach ($items as $it)
                <a href="{{ route($it['route']) }}"
                   class="sa-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:bg-white/5 transition {{ request()->routeIs($it['pattern']) ? 'active' : '' }}">
                    <span class="ico text-slate-500">@include('partials.icon', ['name' => $it['icon'], 'class' => 'w-5 h-5'])</span>
                    {{ $it['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="px-4 py-4 border-t border-white/5">
            <div class="flex items-center gap-3 px-2">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-iris-500 to-acc-500 grid place-items-center text-white text-sm font-semibold">
                    {{ auth()->user()?->initials() }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-[11px] text-acc-400 truncate">Super Administrador</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Salir" class="text-slate-500 hover:text-white transition">
                        @include('partials.icon', ['name' => 'logout', 'class' => 'w-5 h-5'])
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 min-w-0 flex flex-col lg:pl-72">
        <header class="sticky top-0 z-20 h-16 bg-white/80 backdrop-blur border-b border-slate-200/70 flex items-center gap-4 px-4 sm:px-6 lg:px-8">
            <button id="sa-btn-menu" class="lg:hidden text-slate-500">@include('partials.icon', ['name' => 'menu', 'class' => 'w-6 h-6'])</button>
            <div class="flex-1">
                <p class="text-base font-bold text-slate-800 leading-tight">@yield('title', 'Panel')</p>
                <p class="text-xs text-slate-400 leading-tight">Consola de administracion de la plataforma</p>
            </div>
            <span class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-ink-900 text-acc-400 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Plataforma operativa
            </span>
        </header>

        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </main>

        <footer class="px-6 py-4 text-center text-xs text-slate-400">
            Super Admin · Plataforma SaaS Clinica · {{ date('Y') }}
        </footer>
    </div>
</div>

<div id="sa-overlay" class="fixed inset-0 z-30 bg-slate-900/50 hidden lg:hidden"></div>
<script>
    const sb = document.getElementById('sa-sidebar'), ov = document.getElementById('sa-overlay');
    document.getElementById('sa-btn-menu')?.addEventListener('click', () => { sb.classList.remove('-translate-x-full'); ov.classList.remove('hidden'); });
    ov?.addEventListener('click', () => { sb.classList.add('-translate-x-full'); ov.classList.add('hidden'); });
</script>
@stack('scripts')
</body>
</html>
