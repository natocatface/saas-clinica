<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · {{ config('app.name') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:'#f3f1ff',100:'#ebe6ff',200:'#d9cfff',300:'#bda4ff',
                            400:'#9a72ff',500:'#7c44ff',600:'#6d28f5',700:'#5d1ad1',
                            800:'#4d18aa',900:'#3f1788'
                        }
                    },
                    boxShadow: { card: '0 10px 30px -12px rgba(76,40,180,0.18)' }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        body{font-family:'Inter',sans-serif;}
        ::-webkit-scrollbar{width:8px;height:8px}
        ::-webkit-scrollbar-thumb{background:#d9cfff;border-radius:99px}
        ::-webkit-scrollbar-track{background:transparent}
        .sidebar-link.active{background:rgba(255,255,255,.16);color:#fff}
        .sidebar-link.active .ico{color:#fff}
    </style>
    @stack('head')
</head>
<body class="h-full bg-slate-100 text-slate-700 antialiased">
<div class="min-h-full flex" id="app-shell">

    @include('partials.sidebar')

    <!-- Contenido -->
    <div class="flex-1 min-w-0 flex flex-col lg:pl-72">
        @if (session('impersonator_id'))
            <div class="bg-amber-500 text-white text-sm">
                <div class="px-4 sm:px-6 lg:px-8 py-2 flex flex-wrap items-center justify-between gap-2">
                    <span>Estas viendo como <strong>{{ auth()->user()?->clinica?->nombre ?? auth()->user()?->name }}</strong> (modo Super Admin).</span>
                    <form method="POST" action="{{ route('impersonate.stop') }}">
                        @csrf
                        <button class="px-3 py-1 rounded-lg bg-white/20 hover:bg-white/30 font-semibold transition">Volver a Super Admin</button>
                    </form>
                </div>
            </div>
        @endif
        @include('partials.topbar')

        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </main>

        <footer class="px-6 py-4 text-center text-xs text-slate-400">
            {{ config('app.name') }} · Sistema de gestion clinica · {{ date('Y') }}
        </footer>
    </div>
</div>

<!-- overlay movil -->
<div id="sidebar-overlay" class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm hidden lg:hidden"></div>

<script>
    // Toggle sidebar en movil
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('sidebar-overlay');
    function openSidebar(){ sb.classList.remove('-translate-x-full'); ov.classList.remove('hidden'); }
    function closeSidebar(){ sb.classList.add('-translate-x-full'); ov.classList.add('hidden'); }
    document.getElementById('btn-menu')?.addEventListener('click', openSidebar);
    ov?.addEventListener('click', closeSidebar);
</script>
@stack('scripts')
</body>
</html>
