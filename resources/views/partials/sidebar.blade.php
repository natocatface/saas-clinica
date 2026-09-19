@php
    $menu = config('clinic.menu');
    $groupsOrder = config('clinic.groups_order');
    $rolActual = auth()->user()?->role;
    $grouped = [];
    foreach ($menu as $key => $item) {
        $itemRoles = $item['roles'] ?? [];
        $puedeVer = $rolActual === 'admin' || (count($itemRoles) && in_array($rolActual, $itemRoles, true));
        if ($puedeVer) {
            $grouped[$item['group']][$key] = $item;
        }
    }
    $currentModule = request()->route('module');
@endphp

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full lg:translate-x-0 transition-transform duration-300
              bg-gradient-to-b from-brand-700 via-brand-700 to-brand-900 text-brand-100 flex flex-col">

    <!-- Logo -->
    <div class="h-16 flex items-center gap-3 px-6 border-b border-white/10">
        <div class="w-10 h-10 rounded-xl bg-white/15 grid place-items-center text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        </div>
        <div class="leading-tight">
            <p class="text-white font-bold text-base">{{ config('app.name') }}</p>
            <p class="text-[11px] text-brand-200">Gestion Clinica</p>
        </div>
    </div>

    <!-- Navegacion -->
    <nav class="flex-1 overflow-y-auto px-4 py-5 space-y-6">
        <div>
            <a href="{{ route('dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-brand-100 hover:bg-white/10 transition {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="ico text-brand-200">@include('partials.icon', ['name' => 'dashboard', 'class' => 'w-5 h-5'])</span>
                Dashboard
            </a>
        </div>

        @foreach ($groupsOrder as $group)
            @if (!empty($grouped[$group]))
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-wider text-brand-300/80">{{ $group }}</p>
                    <div class="space-y-1">
                        @foreach ($grouped[$group] as $key => $item)
                            @php
                                $rol = auth()->user()?->role;
                                $itemRoles = $item['roles'] ?? [];
                                $puedeVer = $rol === 'admin' || (count($itemRoles) && in_array($rol, $itemRoles, true));
                            @endphp
                            @continue(! $puedeVer)
                            @php
                                $hasRoute = !empty($item['route']);
                                $url = $hasRoute ? route($item['route']) : route('module.show', $key);
                                $prefix = $hasRoute ? explode('.', $item['route'])[0] : null;
                                $isActive = $hasRoute ? request()->routeIs($prefix.'.*') : ($currentModule === $key);
                            @endphp
                            <a href="{{ $url }}"
                               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-brand-100 hover:bg-white/10 transition {{ $isActive ? 'active' : '' }}">
                                <span class="ico text-brand-200">@include('partials.icon', ['name' => $item['icon'], 'class' => 'w-5 h-5'])</span>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    <!-- Usuario -->
    <div class="px-4 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 rounded-full bg-white/20 grid place-items-center text-white text-sm font-semibold">
                {{ auth()->user()?->initials() ?? 'CL' }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()?->name }}</p>
                <p class="text-[11px] text-brand-200 truncate">{{ auth()->user()?->roleLabel() }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Cerrar sesion" class="text-brand-200 hover:text-white transition">
                    @include('partials.icon', ['name' => 'logout', 'class' => 'w-5 h-5'])
                </button>
            </form>
        </div>
    </div>
</aside>
