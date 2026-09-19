@php
    $hora = (int) date('H');
    $saludo = $hora < 12 ? 'Buenos dias' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
    $primerNombre = explode(' ', trim((string) auth()->user()?->name))[0] ?? '';
@endphp
<header class="sticky top-0 z-20 h-16 bg-white/80 backdrop-blur border-b border-slate-200/70 flex items-center gap-4 px-4 sm:px-6 lg:px-8">

    <button id="btn-menu" class="lg:hidden text-slate-500 hover:text-brand-600">
        @include('partials.icon', ['name' => 'menu', 'class' => 'w-6 h-6'])
    </button>

    <div class="hidden sm:block">
        <p class="text-sm text-slate-400 leading-tight">{{ $saludo }},</p>
        <p class="text-base font-bold text-slate-800 leading-tight">{{ $primerNombre }} 👋</p>
    </div>

    <!-- Buscador -->
    <div class="flex-1 max-w-md mx-auto">
        <div class="relative">
            <span class="absolute inset-y-0 left-3 grid place-items-center text-slate-400">
                @include('partials.icon', ['name' => 'search', 'class' => 'w-5 h-5'])
            </span>
            <input type="text" placeholder="Buscar pacientes, citas, medicos..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-full bg-slate-100 border border-transparent text-sm
                          focus:bg-white focus:border-brand-300 focus:ring-2 focus:ring-brand-100 outline-none transition">
        </div>
    </div>

    @php
        $notis = $notificaciones ?? [];
        $tonos = ['brand' => 'bg-brand-50 text-brand-600', 'amber' => 'bg-amber-50 text-amber-600', 'rose' => 'bg-rose-50 text-rose-600'];
    @endphp
    <div class="flex items-center gap-1 sm:gap-2">
        <div class="relative">
            <button type="button" onclick="document.getElementById('noti-panel').classList.toggle('hidden')"
                    class="relative p-2.5 rounded-full text-slate-500 hover:bg-slate-100 hover:text-brand-600 transition">
                @include('partials.icon', ['name' => 'bell', 'class' => 'w-5 h-5'])
                @if (count($notis))
                    <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 grid place-items-center text-[10px] font-bold text-white bg-rose-500 rounded-full ring-2 ring-white">{{ count($notis) }}</span>
                @endif
            </button>
            <div id="noti-panel" class="hidden absolute right-0 mt-2 w-80 rounded-2xl bg-white shadow-xl border border-slate-100 z-30 overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <p class="font-bold text-slate-800 text-sm">Notificaciones</p>
                    <span class="text-xs text-slate-400">{{ count($notis) }}</span>
                </div>
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                    @forelse ($notis as $n)
                        <a href="{{ $n['url'] }}" class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 transition">
                            <span class="w-9 h-9 rounded-xl grid place-items-center shrink-0 {{ $tonos[$n['tone']] ?? 'bg-slate-100 text-slate-500' }}">
                                @include('partials.icon', ['name' => $n['icon'], 'class' => 'w-5 h-5'])
                            </span>
                            <span class="text-sm text-slate-600">{{ $n['texto'] }}</span>
                        </a>
                    @empty
                        <p class="px-4 py-8 text-center text-sm text-slate-400">Sin notificaciones</p>
                    @endforelse
                </div>
            </div>
        </div>
        @if (auth()->user()?->role === 'admin')
            <a href="{{ route('configuracion.index') }}" class="p-2.5 rounded-full text-slate-500 hover:bg-slate-100 hover:text-brand-600 transition">
                @include('partials.icon', ['name' => 'cog', 'class' => 'w-5 h-5'])
            </a>
        @endif
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 grid place-items-center text-white text-sm font-semibold ml-1">
            {{ auth()->user()?->initials() }}
        </div>
    </div>
</header>

<script>
    document.addEventListener('click', function (e) {
        const panel = document.getElementById('noti-panel');
        if (!panel) return;
        if (!e.target.closest('#noti-panel') && !e.target.closest('button[onclick*="noti-panel"]')) {
            panel.classList.add('hidden');
        }
    });
</script>
