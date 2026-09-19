@if (session('ok'))
    <div id="flash" class="mb-5 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="flex-1">{{ session('ok') }}</span>
        <button onclick="document.getElementById('flash').remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
    </div>
    <script>setTimeout(()=>document.getElementById('flash')?.remove(), 4000)</script>
@endif

@if (session('error'))
    <div id="flash-error" class="mb-5 flex items-center gap-3 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 3h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
        <span class="flex-1">{{ session('error') }}</span>
        <button onclick="document.getElementById('flash-error').remove()" class="text-rose-500 hover:text-rose-700">✕</button>
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
        <p class="font-semibold mb-1">Revisa los siguientes errores:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
