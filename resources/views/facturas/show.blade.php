@extends('layouts.app')
@section('title', 'Factura '.$factura->numero)

@php
    $estadoColor = [
        'pendiente' => 'bg-amber-100 text-amber-700',
        'pagada' => 'bg-emerald-100 text-emerald-700',
        'anulada' => 'bg-rose-100 text-rose-700',
    ];
@endphp

@push('head')
<style>
@media print {
    #sidebar, header, footer, .no-print { display: none !important; }
    .lg\:pl-72 { padding-left: 0 !important; }
    body { background: #fff !important; }
    .print-card { box-shadow: none !important; }
    main { padding: 0 !important; }
}
</style>
@endpush

@section('content')
    <div class="no-print flex flex-wrap items-center justify-between gap-3 mb-6">
        <nav class="text-sm text-slate-400">
            <a href="{{ route('facturas.index') }}" class="hover:text-brand-600">Facturacion</a>
            <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">{{ $factura->numero }}</span>
        </nav>
        <div class="flex items-center gap-2">
            @if ($factura->estado === 'pendiente')
                <form method="POST" action="{{ route('facturas.pagar', $factura) }}">
                    @csrf @method('PATCH')
                    <button class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">Marcar pagada</button>
                </form>
            @endif
            <a href="{{ route('facturas.edit', $factura) }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-medium text-slate-600 hover:border-slate-300 transition">Editar</a>
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30">Imprimir</button>
        </div>
    </div>

    <div class="print-card max-w-3xl mx-auto rounded-3xl bg-white shadow-card p-8 sm:p-10">
        <div class="flex items-start justify-between border-b border-slate-200 pb-5 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-brand-600 text-white grid place-items-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <div>
                    <p class="text-lg font-extrabold text-slate-800">{{ config('app.name') }}</p>
                    <p class="text-xs text-slate-400">Factura / Recibo</p>
                </div>
            </div>
            <div class="text-right text-sm">
                <p class="font-bold text-slate-800">{{ $factura->numero }}</p>
                <p class="text-slate-400">{{ $factura->fecha->format('d/m/Y') }}</p>
                <span class="inline-block mt-1 text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$factura->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $factura->estadoLabel() }}</span>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-xs uppercase tracking-wide text-slate-400">Paciente</p>
                <p class="font-semibold text-slate-800">{{ $factura->paciente?->nombre_completo }}</p>
                <p class="text-slate-500">{{ $factura->paciente?->ci ? 'CI: '.$factura->paciente->ci : '' }}</p>
            </div>
            <div class="sm:text-right">
                <p class="text-xs uppercase tracking-wide text-slate-400">Metodo de pago</p>
                <p class="font-semibold text-slate-800">{{ $factura->metodo_pago ? ucfirst($factura->metodo_pago) : '—' }}</p>
            </div>
        </div>

        <table class="w-full text-sm mb-6">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-slate-400 border-b border-slate-200">
                    <th class="py-2 font-semibold">Descripcion</th>
                    <th class="py-2 font-semibold text-right">Cant.</th>
                    <th class="py-2 font-semibold text-right">P. Unit.</th>
                    <th class="py-2 font-semibold text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($factura->items as $item)
                    <tr>
                        <td class="py-2.5 text-slate-700">{{ $item->descripcion }}</td>
                        <td class="py-2.5 text-right text-slate-500">{{ rtrim(rtrim(number_format($item->cantidad,2),'0'),'.') }}</td>
                        <td class="py-2.5 text-right text-slate-500">{{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="py-2.5 text-right font-medium text-slate-700">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ml-auto w-full sm:w-64 space-y-2 text-sm">
            <div class="flex justify-between text-slate-500"><span>Subtotal</span><span class="font-semibold text-slate-700">{{ money($factura->subtotal) }}</span></div>
            <div class="flex justify-between text-slate-500"><span>Descuento</span><span class="font-semibold text-slate-700">{{ money($factura->descuento) }}</span></div>
            <div class="flex justify-between border-t border-slate-200 pt-2 text-base"><span class="font-semibold text-slate-700">Total</span><span class="font-extrabold text-brand-600">{{ money($factura->total) }}</span></div>
        </div>

        @if ($factura->comprobante())
            <div class="mt-6 border-t border-slate-200 pt-4 flex items-center gap-4">
                @if (! empty($qr))
                    <div id="sunat-qr" data-qr="{{ $qr }}" class="shrink-0"></div>
                @endif
                <div class="text-xs text-slate-500 leading-relaxed">
                    <p>Representacion impresa del Comprobante de Pago Electronico</p>
                    <p class="font-semibold text-slate-700">{{ $factura->tipoComprobanteLabel() }} · {{ $factura->comprobante() }}</p>
                    @if ($factura->sunat_hash)
                        <p>Autorizacion / Hash: <span class="font-mono">{{ $factura->sunat_hash }}</span></p>
                    @endif
                    @if ($factura->anulado())
                        <p class="text-rose-600 font-semibold mt-1">ANULADO mediante Nota de Credito {{ $factura->notaCredito() }}</p>
                    @endif
                </div>
            </div>
        @endif

        @if ($factura->notas)
            <p class="mt-4 text-xs text-slate-400 border-t border-slate-200 pt-4">{{ $factura->notas }}</p>
        @endif
    </div>

    {{-- Panel de Facturacion Electronica (SUNAT) --}}
    @php
        $sunatColor = [
            'no_enviado' => 'bg-slate-100 text-slate-600',
            'pendiente'  => 'bg-amber-100 text-amber-700',
            'aceptado'   => 'bg-emerald-100 text-emerald-700',
            'observado'  => 'bg-sky-100 text-sky-700',
            'rechazado'  => 'bg-rose-100 text-rose-700',
            'anulado'    => 'bg-slate-200 text-slate-600',
        ];
        $yaAceptado = in_array($factura->sunat_estado, ['aceptado', 'observado'], true);
    @endphp
    <div class="no-print max-w-3xl mx-auto mt-6 rounded-3xl bg-white shadow-card p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-brand-50 text-brand-600 grid place-items-center">
                    @include('partials.icon', ['name' => 'receipt', 'class' => 'w-6 h-6'])
                </div>
                <div>
                    <p class="font-bold text-slate-800">Facturacion Electronica · SUNAT</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $sunatColor[$factura->sunat_estado] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $factura->sunatEstadoLabel() }}
                        </span>
                        @if ($factura->comprobante())
                            <span class="text-xs text-slate-500">{{ $factura->comprobante() }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('facturas.emitir', $factura) }}"
                      onsubmit="return confirm('Se firmara y enviara el comprobante a SUNAT. ¿Continuar?');">
                    @csrf
                    <button {{ $yaAceptado ? 'disabled' : '' }}
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition shadow-lg
                                {{ $yaAceptado ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-brand-600 text-white hover:bg-brand-700 shadow-brand-500/30' }}">
                        @include('partials.icon', ['name' => 'bolt', 'class' => 'w-4 h-4'])
                        {{ $yaAceptado ? 'Ya emitida' : ($factura->sunat_estado === 'rechazado' ? 'Reintentar envio' : 'Emitir a SUNAT') }}
                    </button>
                </form>
            </div>
        </div>

        @if ($factura->sunat_mensaje)
            <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <span class="font-semibold text-slate-700">Respuesta SUNAT:</span>
                @if ($factura->sunat_codigo) <span class="text-slate-400">[{{ $factura->sunat_codigo }}]</span> @endif
                {{ $factura->sunat_mensaje }}
            </div>
        @endif

        @if ($factura->xml_path || $factura->cdr_path || $factura->nc_xml_path || $factura->nc_cdr_path)
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <span class="text-xs text-slate-400 mr-1">Descargar:</span>
                @if ($factura->xml_path)
                    <a href="{{ route('facturas.descargar', [$factura, 'xml']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">XML</a>
                @endif
                @if ($factura->cdr_path)
                    <a href="{{ route('facturas.descargar', [$factura, 'cdr']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-slate-200 transition">CDR</a>
                @endif
                @if ($factura->nc_xml_path)
                    <a href="{{ route('facturas.descargar', [$factura, 'nc-xml']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-semibold hover:bg-rose-100 transition">XML NC</a>
                @endif
                @if ($factura->nc_cdr_path)
                    <a href="{{ route('facturas.descargar', [$factura, 'nc-cdr']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-semibold hover:bg-rose-100 transition">CDR NC</a>
                @endif
            </div>
        @endif

        @if ($factura->enviado_at)
            <p class="mt-3 text-xs text-slate-400">Ultimo envio: {{ $factura->enviado_at->format('d/m/Y H:i') }}</p>
        @endif

        {{-- Nota de Credito (anulacion) --}}
        @if ($factura->nc_serie || $yaAceptado)
            <div class="mt-5 pt-5 border-t border-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Nota de Credito {{ $factura->anulado() ? '· Anulada' : '(anulacion)' }}</p>
                        @if ($factura->notaCredito())
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $factura->notaCredito() }}
                                @if ($factura->nc_estado)
                                    · <span class="font-medium {{ $factura->nc_estado === 'aceptado' ? 'text-emerald-600' : ($factura->nc_estado === 'rechazado' ? 'text-rose-600' : 'text-amber-600') }}">
                                        {{ ucfirst($factura->nc_estado) }}
                                    </span>
                                @endif
                                @if ($factura->nc_motivo_desc) · {{ $factura->nc_motivo_desc }} @endif
                            </p>
                        @else
                            <p class="text-xs text-slate-500 mt-0.5">Emite una nota de credito para anular este comprobante ante SUNAT.</p>
                        @endif
                    </div>
                </div>

                @if ($factura->nc_mensaje)
                    <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                        <span class="font-semibold text-slate-700">Respuesta SUNAT (NC):</span>
                        @if ($factura->nc_codigo) <span class="text-slate-400">[{{ $factura->nc_codigo }}]</span> @endif
                        {{ $factura->nc_mensaje }}
                    </div>
                @endif

                @if (! $factura->anulado())
                    <form method="POST" action="{{ route('facturas.anular', $factura) }}"
                          onsubmit="return confirm('Se emitira una Nota de Credito que anula este comprobante ante SUNAT. ¿Continuar?');"
                          class="mt-4 grid sm:grid-cols-[1fr,1.4fr,auto] gap-3 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Motivo</label>
                            <select name="motivo" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm bg-white focus:border-brand-500 outline-none transition">
                                @foreach (config('facturacion.motivos_nc') as $cod => $desc)
                                    <option value="{{ $cod }}" @selected($cod === '01')>{{ $cod }} · {{ $desc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Descripcion (opcional)</label>
                            <input type="text" name="motivo_desc" maxlength="250" placeholder="Detalle del motivo"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:border-brand-500 outline-none transition">
                        </div>
                        <button class="px-4 py-2 rounded-xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 transition shadow-lg shadow-rose-500/30 whitespace-nowrap">
                            {{ $factura->nc_estado === 'rechazado' ? 'Reintentar NC' : 'Anular con NC' }}
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    @if (! empty($qr))
        @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            (function () {
                var el = document.getElementById('sunat-qr');
                if (el && el.dataset.qr && window.QRCode) {
                    new QRCode(el, { text: el.dataset.qr, width: 96, height: 96, correctLevel: QRCode.CorrectLevel.M });
                }
            })();
        </script>
        @endpush
    @endif
@endsection
