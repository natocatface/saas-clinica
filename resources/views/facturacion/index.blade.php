@extends('layouts.app')
@section('title', 'Facturacion Electronica')

@php
    $chk = fn ($v) => (string) $v === '1';
@endphp

@section('content')
    {{-- Encabezado --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
            @include('partials.icon', ['name' => 'receipt', 'class' => 'w-6 h-6'])
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">Facturacion Electronica</h1>
            <p class="text-sm text-slate-500">Emision de comprobantes ante SUNAT · Peru</p>
        </div>
    </div>

    @include('partials.flash')

    {{-- Banner de estado --}}
    <div class="rounded-3xl bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 text-white shadow-card overflow-hidden mb-6">
        <div class="p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/15 grid place-items-center shrink-0">
                        @include('partials.icon', ['name' => 'receipt', 'class' => 'w-7 h-7'])
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold">Facturacion Electronica</h2>
                            <span class="text-sm">🇵🇪</span>
                            <span class="text-xs font-semibold bg-white/15 px-2 py-0.5 rounded-full">Peru</span>
                        </div>
                        <p class="text-sm text-brand-100/90 mt-1 max-w-xl">
                            Emision de comprobantes electronicos ante <strong>SUNAT</strong> · UBL 2.1 ·
                            Boletas, facturas y notas de credito.
                        </p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <span class="inline-block text-[11px] font-bold bg-white/20 px-3 py-1 rounded-lg tracking-wide">SUNAT</span>
                    <p class="text-[11px] text-brand-100/80 mt-1">Comprobantes de Pago Electronicos</p>
                </div>
            </div>

            {{-- Badges de estado --}}
            <div class="flex flex-wrap items-center gap-2 mt-5">
                @if ($estado['habilitado'])
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-emerald-400/20 text-emerald-100 px-3 py-1.5 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-emerald-300"></span> Habilitada
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-white/10 text-brand-100 px-3 py-1.5 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span> Deshabilitada
                    </span>
                @endif

                <span class="text-xs font-semibold bg-white/10 px-3 py-1.5 rounded-full">
                    Driver: {{ $estado['driver'] === 'none' ? 'Ninguno' : ucfirst($estado['driver']) }}
                </span>
                <span class="text-xs font-semibold bg-white/10 px-3 py-1.5 rounded-full">
                    Modo: {{ $estado['entorno'] === 'produccion' ? 'Produccion' : 'Beta' }}
                </span>

                @if ($estado['driver'] === 'greenter')
                    @if ($estado['certificado'])
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-emerald-400/20 text-emerald-100 px-3 py-1.5 rounded-full">
                            @include('partials.icon', ['name' => 'check-badge', 'class' => 'w-3.5 h-3.5']) Certificado OK
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-rose-400/20 text-rose-100 px-3 py-1.5 rounded-full">
                            ✕ Certificado no encontrado
                        </span>
                    @endif
                @endif
            </div>
        </div>

        <div class="bg-black/10 px-6 sm:px-8 py-4 flex flex-col sm:flex-row sm:flex-wrap sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-brand-100/80">
                @if ($estado['listo'])
                    Todo listo para emitir comprobantes electronicos.
                @else
                    Completa la configuracion para comenzar a emitir.
                @endif
            </p>
            <form method="POST" action="{{ route('facturacion.probar') }}" class="w-full sm:w-auto">
                @csrf
                <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white text-brand-700 text-sm font-semibold hover:bg-brand-50 transition shadow">
                    @include('partials.icon', ['name' => 'bolt', 'class' => 'w-4 h-4'])
                    Probar conexion con SUNAT
                </button>
            </form>
        </div>
    </div>

    {{-- Formulario --}}
    <form method="POST" action="{{ route('facturacion.update') }}" class="space-y-6">
        @csrf @method('PUT')

        {{-- Estado y modo --}}
        <div class="rounded-3xl bg-white shadow-card p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 grid place-items-center">
                    @include('partials.icon', ['name' => 'bolt', 'class' => 'w-5 h-5'])
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Estado y modo</h3>
                    <p class="text-xs text-slate-500">Activacion, forma de emision y entorno de SUNAT</p>
                </div>
            </div>

            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 p-4 mb-3 cursor-pointer hover:border-brand-300 transition">
                <input type="checkbox" name="habilitado" value="1" @checked($chk($config['habilitado']))
                       class="mt-0.5 w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Habilitar facturacion electronica</span>
                    <span class="block text-xs text-slate-500">Si esta desactivada, las facturas no generan comprobante ante SUNAT.</span>
                </span>
            </label>

            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 p-4 mb-3 cursor-pointer hover:border-brand-300 transition">
                <input type="checkbox" name="auto_emitir" value="1" @checked($chk($config['auto_emitir']))
                       class="mt-0.5 w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Emitir automaticamente al registrar la factura</span>
                    <span class="block text-xs text-slate-500">Cada boleta o factura se envia apenas se registra en el sistema.</span>
                </span>
            </label>

            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 p-4 mb-5 cursor-pointer hover:border-brand-300 transition">
                <input type="checkbox" name="precios_incluyen_igv" value="1" @checked(($config['precios_incluyen_igv'] ?? '1') !== '0')
                       class="mt-0.5 w-5 h-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Los precios ya incluyen IGV ({{ config('facturacion.igv_porcentaje') }}%)</span>
                    <span class="block text-xs text-slate-500">Activado: el IGV se extrae del importe. Desactivado: el IGV se suma sobre la base imponible.</span>
                </span>
            </label>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Driver de emision</label>
                    <select name="driver" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                        @foreach ($drivers as $val => $label)
                            <option value="{{ $val }}" @selected($config['driver'] === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Entorno SUNAT</label>
                    <select name="entorno" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none transition">
                        @foreach ($entornos as $val => $label)
                            <option value="{{ $val }}" @selected(($config['entorno'] ?? 'beta') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-3 gap-5 mt-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Serie Factura</label>
                    <input type="text" name="serie_factura" value="{{ old('serie_factura', $config['serie_factura'] ?? 'F001') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Serie Boleta</label>
                    <input type="text" name="serie_boleta" value="{{ old('serie_boleta', $config['serie_boleta'] ?? 'B001') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Comprobante por defecto</label>
                    <select name="tipo_default" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 outline-none transition">
                        @foreach ($tipos as $val => $label)
                            <option value="{{ $val }}" @selected(($config['tipo_default'] ?? '01') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-5 mt-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Serie Nota de Credito (Factura)</label>
                    <input type="text" name="serie_nc_factura" value="{{ old('serie_nc_factura', $config['serie_nc_factura'] ?? 'FC01') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Serie Nota de Credito (Boleta)</label>
                    <input type="text" name="serie_nc_boleta" value="{{ old('serie_nc_boleta', $config['serie_nc_boleta'] ?? 'BC01') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
            </div>
        </div>

        {{-- Datos del emisor --}}
        <div class="rounded-3xl bg-white shadow-card p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 grid place-items-center">
                    @include('partials.icon', ['name' => 'building', 'class' => 'w-5 h-5'])
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Datos del emisor</h3>
                    <p class="text-xs text-slate-500">Aparecen en el comprobante electronico</p>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">RUC <span class="text-rose-500">*</span></label>
                    <input type="text" name="ruc" value="{{ old('ruc', $config['ruc']) }}" placeholder="20000000001"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Razon social <span class="text-rose-500">*</span></label>
                    <input type="text" name="razon_social" value="{{ old('razon_social', $config['razon_social']) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre comercial</label>
                    <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial', $config['nombre_comercial']) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Direccion fiscal</label>
                    <input type="text" name="direccion" value="{{ old('direccion', $config['direccion']) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ubigeo</label>
                    <input type="text" name="ubigeo" value="{{ old('ubigeo', $config['ubigeo'] ?? '150101') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Departamento</label>
                    <input type="text" name="departamento" value="{{ old('departamento', $config['departamento'] ?? 'LIMA') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Provincia</label>
                    <input type="text" name="provincia" value="{{ old('provincia', $config['provincia'] ?? 'LIMA') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Distrito</label>
                    <input type="text" name="distrito" value="{{ old('distrito', $config['distrito'] ?? 'LIMA') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
            </div>
        </div>

        {{-- Credenciales SUNAT --}}
        <div class="rounded-3xl bg-white shadow-card p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 grid place-items-center">
                    @include('partials.icon', ['name' => 'key', 'class' => 'w-5 h-5'])
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Credenciales SUNAT</h3>
                    <p class="text-xs text-slate-500">Clave SOL y certificado digital</p>
                </div>
            </div>

            <div class="flex items-start gap-2 rounded-xl bg-brand-50 border border-brand-100 px-4 py-3 text-sm text-brand-700 mb-5">
                <span>ℹ️</span>
                <p>En <strong>beta</strong> puedes usar RUC <strong>{{ $beta['ruc'] }}</strong> con usuario y clave <strong>{{ $beta['usuario'] }}</strong>.</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Usuario Clave SOL</label>
                    <input type="text" name="sol_usuario" value="{{ old('sol_usuario', $config['sol_usuario']) }}" placeholder="MODDATOS"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Clave SOL</label>
                    <input type="password" name="sol_clave" value="" autocomplete="new-password"
                           placeholder="{{ $config['sol_clave'] ? '•••••••• (sin cambios)' : 'Ingresa la clave' }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                    <p class="text-[11px] text-slate-400 mt-1">Se guarda cifrada. Dejala en blanco para no modificarla.</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ruta del certificado (.pem)</label>
                    <input type="text" name="cert_ruta" value="{{ old('cert_ruta', $config['cert_ruta']) }}"
                           placeholder="C:\SAAS\saas_clinica\storage\app\facturacion\pe\certificate.pem"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                    @if ($config['cert_ruta'] && ! $estado['certificado'])
                        <p class="text-xs text-rose-600 mt-1.5">⚠ No se encontro el certificado en la ruta indicada.</p>
                    @elseif ($estado['certificado'])
                        <p class="text-xs text-emerald-600 mt-1.5">✓ Certificado encontrado.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <a href="{{ route('dashboard') }}" class="text-center px-4 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-slate-700 transition">← Volver</a>
            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => 'check-badge', 'class' => 'w-4 h-4'])
                Guardar configuracion
            </button>
        </div>
    </form>
@endsection
