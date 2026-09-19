<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Support\Facturacion\FacturacionElectronica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacturacionElectronicaController extends Controller
{
    public function __construct(private FacturacionElectronica $fe)
    {
    }

    /** Pantalla de configuracion de Facturacion Electronica. */
    public function index()
    {
        return view('facturacion.index', [
            'config'  => $this->fe->todo(),
            'estado'  => $this->fe->estado(),
            'drivers' => config('facturacion.drivers'),
            'entornos' => config('facturacion.entornos'),
            'tipos'   => config('facturacion.tipos_comprobante'),
            'beta'    => config('facturacion.beta'),
        ]);
    }

    /** Guarda la configuracion. */
    public function update(Request $request)
    {
        $data = $request->validate([
            'habilitado'       => ['nullable', 'boolean'],
            'auto_emitir'      => ['nullable', 'boolean'],
            'precios_incluyen_igv' => ['nullable', 'boolean'],
            'driver'           => ['required', 'in:'.implode(',', array_keys(config('facturacion.drivers')))],
            'entorno'          => ['required', 'in:'.implode(',', array_keys(config('facturacion.entornos')))],
            'ruc'              => ['nullable', 'string', 'max:11'],
            'razon_social'     => ['nullable', 'string', 'max:255'],
            'nombre_comercial' => ['nullable', 'string', 'max:255'],
            'direccion'        => ['nullable', 'string', 'max:255'],
            'ubigeo'           => ['nullable', 'string', 'max:6'],
            'departamento'     => ['nullable', 'string', 'max:100'],
            'provincia'        => ['nullable', 'string', 'max:100'],
            'distrito'         => ['nullable', 'string', 'max:100'],
            'sol_usuario'      => ['nullable', 'string', 'max:100'],
            'sol_clave'        => ['nullable', 'string', 'max:255'],
            'cert_ruta'        => ['nullable', 'string', 'max:255'],
            'serie_factura'    => ['nullable', 'string', 'max:8'],
            'serie_boleta'     => ['nullable', 'string', 'max:8'],
            'serie_nc_factura' => ['nullable', 'string', 'max:8'],
            'serie_nc_boleta'  => ['nullable', 'string', 'max:8'],
            'tipo_default'     => ['nullable', 'in:01,03'],
        ]);

        $this->fe->set('habilitado', $request->boolean('habilitado') ? '1' : '0');
        $this->fe->set('auto_emitir', $request->boolean('auto_emitir') ? '1' : '0');
        $this->fe->set('precios_incluyen_igv', $request->boolean('precios_incluyen_igv') ? '1' : '0');

        foreach (['driver', 'entorno', 'ruc', 'razon_social', 'nombre_comercial', 'direccion',
            'ubigeo', 'departamento', 'provincia', 'distrito', 'sol_usuario',
            'cert_ruta', 'serie_factura', 'serie_boleta', 'tipo_default',
            'serie_nc_factura', 'serie_nc_boleta'] as $clave) {
            $this->fe->set($clave, $data[$clave] ?? null);
        }

        // La Clave SOL solo se actualiza si el usuario escribio una nueva.
        if (! empty($data['sol_clave'])) {
            $this->fe->set('sol_clave', $data['sol_clave']);
        }

        return redirect()->route('facturacion.index')->with('ok', 'Configuracion de facturacion electronica guardada.');
    }

    /** Reporte / listado de comprobantes electronicos emitidos. */
    public function comprobantes(Request $request)
    {
        $estado = $request->string('estado')->toString();
        $tipo   = $request->string('tipo')->toString();
        $q      = $request->string('q')->toString();
        $desde  = $request->date('desde');
        $hasta  = $request->date('hasta');

        $base = Factura::query()
            ->with('paciente')
            ->whereNotNull('tipo_comprobante')
            ->when($estado, fn ($x) => $x->where('sunat_estado', $estado))
            ->when($tipo, fn ($x) => $x->where('tipo_comprobante', $tipo))
            ->when($desde, fn ($x) => $x->whereDate('fecha', '>=', $desde))
            ->when($hasta, fn ($x) => $x->whereDate('fecha', '<=', $hasta))
            ->when($q, function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('serie', 'like', "%{$q}%")
                        ->orWhere('correlativo', 'like', "%{$q}%")
                        ->orWhere('numero', 'like', "%{$q}%")
                        ->orWhereHas('paciente', fn ($p) => $p->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%"));
                });
            })
            ->orderByDesc('fecha')->orderByDesc('id');

        // Exportacion CSV
        if ($request->get('export') === 'csv') {
            $filas = (clone $base)->get()->map(fn ($f) => [
                $f->comprobante(), $f->tipoComprobanteLabel(), $f->fecha?->format('d/m/Y'),
                $f->paciente?->nombre_completo, number_format((float) $f->total, 2),
                number_format((float) $f->igv, 2), $f->sunatEstadoLabel(), $f->sunat_hash,
            ]);

            return csv_descarga('comprobantes_electronicos.csv',
                ['Comprobante', 'Tipo', 'Fecha', 'Paciente', 'Total', 'IGV', 'Estado SUNAT', 'Hash'],
                $filas);
        }

        // Resumen por estado (Eloquent: respeta el aislamiento por clinica)
        $porEstado = (clone $base)->reorder()
            ->selectRaw('sunat_estado, count(*) as c')
            ->groupBy('sunat_estado')
            ->pluck('c', 'sunat_estado');

        $resumen = [
            'total'     => (clone $base)->count(),
            'aceptado'  => (int) ($porEstado['aceptado'] ?? 0) + (int) ($porEstado['observado'] ?? 0),
            'pendiente' => (int) ($porEstado['pendiente'] ?? 0) + (int) ($porEstado['no_enviado'] ?? 0),
            'rechazado' => (int) ($porEstado['rechazado'] ?? 0),
            'anulado'   => (int) ($porEstado['anulado'] ?? 0),
        ];

        return view('facturacion.comprobantes', [
            'comprobantes' => $base->paginate(15)->withQueryString(),
            'resumen'  => $resumen,
            'estados'  => Factura::SUNAT_ESTADOS,
            'tipos'    => config('facturacion.tipos_comprobante'),
            'filtros'  => compact('estado', 'tipo', 'q') + [
                'desde' => $request->string('desde')->toString(),
                'hasta' => $request->string('hasta')->toString(),
            ],
        ]);
    }

    /** Prueba la conexion / credenciales contra SUNAT. */
    public function probar()
    {
        $r = $this->fe->probarConexion();

        return redirect()->route('facturacion.index')
            ->with($r->exito ? 'ok' : 'error', $r->mensaje);
    }

    /** Emite (firma y envia) una factura existente ante SUNAT. */
    public function emitir(Factura $factura)
    {
        if (! $this->fe->habilitado()) {
            return back()->with('error', 'La facturacion electronica esta deshabilitada. Activala en Configuracion.');
        }

        if (in_array($factura->sunat_estado, ['aceptado', 'observado'], true)) {
            return back()->with('error', 'Esta factura ya fue aceptada por SUNAT.');
        }

        $r = $this->fe->emitirFactura($factura);

        return back()->with($r->exito ? 'ok' : 'error',
            ($r->exito ? 'Comprobante emitido: ' : 'No se pudo emitir: ').$r->mensaje);
    }

    /** Descarga el XML firmado o el CDR (de la factura o de su nota de credito). */
    public function descargar(Factura $factura, string $archivo)
    {
        $mapa = [
            'xml'    => ['col' => 'xml_path', 'ext' => 'xml'],
            'cdr'    => ['col' => 'cdr_path', 'ext' => 'zip'],
            'nc-xml' => ['col' => 'nc_xml_path', 'ext' => 'xml'],
            'nc-cdr' => ['col' => 'nc_cdr_path', 'ext' => 'zip'],
        ];

        abort_unless(isset($mapa[$archivo]), 404);

        $ruta = $factura->{$mapa[$archivo]['col']};
        abort_if(! $ruta || ! Storage::exists($ruta), 404, 'Archivo no disponible.');

        $nombre = ($factura->comprobante() ?: $factura->numero)
            .($archivo === 'cdr' ? '-CDR' : (str_starts_with($archivo, 'nc') ? '-NC' : ''))
            .'.'.$mapa[$archivo]['ext'];

        return Storage::download($ruta, $nombre);
    }

    /** Emite una Nota de Credito para anular/corregir una factura ya aceptada. */
    public function anular(Request $request, Factura $factura)
    {
        $data = $request->validate([
            'motivo'      => ['required', 'in:'.implode(',', array_keys(config('facturacion.motivos_nc')))],
            'motivo_desc' => ['nullable', 'string', 'max:250'],
        ]);

        if (! $this->fe->habilitado()) {
            return back()->with('error', 'La facturacion electronica esta deshabilitada.');
        }

        if (! in_array($factura->sunat_estado, ['aceptado', 'observado'], true)) {
            return back()->with('error', 'Solo se puede anular un comprobante aceptado por SUNAT.');
        }

        if ($factura->nc_estado === 'aceptado') {
            return back()->with('error', 'Esta factura ya tiene una nota de credito aceptada.');
        }

        // Serie / correlativo de la Nota de Credito.
        $serie = $factura->nc_serie ?: $this->fe->serieNota($factura->tipo_comprobante ?: '01');
        $correlativo = $factura->nc_correlativo ?: Factura::siguienteCorrelativoNota($serie);
        $desMotivo = $data['motivo_desc'] ?: config('facturacion.motivos_nc')[$data['motivo']];

        $factura->fill([
            'nc_serie' => $serie,
            'nc_correlativo' => $correlativo,
            'nc_motivo' => $data['motivo'],
            'nc_motivo_desc' => $desMotivo,
        ])->save();

        $factura->load(['items', 'paciente']);

        $r = $this->fe->anular($factura, $data['motivo'], $desMotivo);

        $factura->update([
            'nc_estado'   => $r->estado,
            'nc_codigo'   => $r->codigo,
            'nc_mensaje'  => $r->mensaje,
            'nc_xml_path' => $r->xmlPath,
            'nc_cdr_path' => $r->cdrPath,
            'anulado_at'  => now(),
            // Si SUNAT acepta la NC, el comprobante queda anulado y la factura tambien.
            'sunat_estado' => in_array($r->estado, ['aceptado', 'observado'], true) ? 'anulado' : $factura->sunat_estado,
            'estado' => in_array($r->estado, ['aceptado', 'observado'], true) ? 'anulada' : $factura->estado,
        ]);

        return back()->with($r->exito ? 'ok' : 'error',
            ($r->exito ? 'Nota de credito emitida: ' : 'No se pudo anular: ').$r->mensaje);
    }
}
