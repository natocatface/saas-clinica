<?php

namespace App\Support\Facturacion;

use App\Models\Configuracion;
use App\Models\Factura;
use App\Support\Facturacion\Contracts\EmisorSunat;
use App\Support\Facturacion\Drivers\GreenterEmisor;
use App\Support\Facturacion\Drivers\NullEmisor;
use Illuminate\Support\Facades\Crypt;

/**
 * Fachada de Facturacion Electronica (SUNAT - Peru).
 *
 * Centraliza la lectura/escritura de la configuracion (guardada en la tabla
 * `configuraciones` con el prefijo `fe_` y aislada por clinica), resuelve el
 * driver de emision activo y expone el estado general del modulo para pintar
 * los badges del panel de configuracion.
 */
class FacturacionElectronica
{
    private const PREFIJO = 'fe_';

    /** Claves cuyo valor se guarda cifrado. */
    private const SECRETAS = ['sol_clave'];

    /* --------------------------------------------------------------------- */
    /* Configuracion                                                          */
    /* --------------------------------------------------------------------- */

    /** Lee un valor de configuracion (desencripta las claves secretas). */
    public function get(string $clave, ?string $default = null): ?string
    {
        $valor = Configuracion::get(self::PREFIJO.$clave, null);

        if ($valor === null || $valor === '') {
            return $default;
        }

        if (in_array($clave, self::SECRETAS, true)) {
            try {
                return Crypt::decryptString($valor);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return $valor;
    }

    /** Guarda un valor de configuracion (cifra las claves secretas). */
    public function set(string $clave, ?string $valor): void
    {
        if (in_array($clave, self::SECRETAS, true) && $valor !== null && $valor !== '') {
            $valor = Crypt::encryptString($valor);
        }

        Configuracion::set(self::PREFIJO.$clave, $valor);
    }

    /** Devuelve todas las claves de configuracion como arreglo asociativo. */
    public function todo(): array
    {
        $data = [];
        foreach (config('facturacion.claves') as $clave) {
            $data[$clave] = $this->get($clave);
        }

        return $data;
    }

    /* --------------------------------------------------------------------- */
    /* Estado                                                                 */
    /* --------------------------------------------------------------------- */

    public function habilitado(): bool
    {
        return (string) $this->get('habilitado') === '1';
    }

    public function autoEmitir(): bool
    {
        return (string) $this->get('auto_emitir') === '1';
    }

    public function driver(): string
    {
        return $this->get('driver', config('facturacion.driver_default')) ?: 'none';
    }

    public function entorno(): string
    {
        return $this->get('entorno', 'beta') ?: 'beta';
    }

    /**
     * Indica si los importes guardados en las facturas ya incluyen el IGV.
     * Por defecto true (precio de venta al publico). Si es false, el importe
     * de cada linea se considera base imponible y el IGV se suma encima.
     */
    public function preciosIncluyenIgv(): bool
    {
        $v = $this->get('precios_incluyen_igv');

        return $v === null ? true : $v === '1';
    }

    public function certificadoRuta(): ?string
    {
        return $this->get('cert_ruta');
    }

    public function certificadoExiste(): bool
    {
        $ruta = $this->certificadoRuta();

        return $ruta ? is_file($ruta) : false;
    }

    /** Serie por defecto segun el tipo de comprobante (01/03). */
    public function serie(string $tipo): string
    {
        $clave = $tipo === '01' ? 'serie_factura' : 'serie_boleta';

        return $this->get($clave) ?: config("facturacion.series.$tipo", 'F001');
    }

    /** Serie de Nota de Credito segun el tipo del comprobante afectado (01/03). */
    public function serieNota(string $tipoAfectado): string
    {
        $clave = $tipoAfectado === '01' ? 'serie_nc_factura' : 'serie_nc_boleta';

        return $this->get($clave) ?: config("facturacion.series_nc.$tipoAfectado", 'FC01');
    }

    /**
     * Resumen del estado para los badges del panel de configuracion.
     */
    public function estado(): array
    {
        return [
            'habilitado'  => $this->habilitado(),
            'driver'      => $this->driver(),
            'entorno'     => $this->entorno(),
            'certificado' => $this->certificadoExiste(),
            'listo'       => $this->habilitado()
                && $this->driver() !== 'none'
                && ($this->driver() !== 'greenter' || $this->certificadoExiste()),
        ];
    }

    /* --------------------------------------------------------------------- */
    /* Emision                                                                */
    /* --------------------------------------------------------------------- */

    /** Resuelve el driver de emision activo. */
    public function emisor(): EmisorSunat
    {
        return match ($this->driver()) {
            'greenter' => new GreenterEmisor($this),
            default    => new NullEmisor(),
        };
    }

    /** Emite (firma y envia) una factura ante SUNAT usando el driver activo. */
    public function emitir(Factura $factura): ResultadoEmision
    {
        return $this->emisor()->emitir($factura);
    }

    /**
     * Prepara (tipo/serie/correlativo), emite y persiste el resultado en la
     * factura. Es el punto unico usado tanto por el boton "Emitir" como por
     * la emision automatica al registrar la factura.
     */
    public function emitirFactura(Factura $factura): ResultadoEmision
    {
        $tipo = $factura->tipo_comprobante ?: ($this->get('tipo_default') ?: '01');
        $serie = $factura->serie ?: $this->serie($tipo);
        $correlativo = $factura->correlativo ?: Factura::siguienteCorrelativo($serie);

        $factura->fill([
            'tipo_comprobante' => $tipo,
            'serie' => $serie,
            'correlativo' => $correlativo,
            'moneda_iso' => 'PEN',
        ])->save();

        $factura->loadMissing(['items', 'paciente']);

        $r = $this->emitir($factura);

        $igvPct = (float) config('facturacion.igv_porcentaje', 18);
        $total = (float) $factura->total;
        $igv = $this->preciosIncluyenIgv()
            ? round($total - $total / (1 + $igvPct / 100), 2)
            : round($total * $igvPct / 100, 2);

        $factura->update([
            'sunat_estado' => $r->estado,
            'sunat_hash'   => $r->hash,
            'sunat_codigo' => $r->codigo,
            'sunat_mensaje' => $r->mensaje,
            'xml_path'     => $r->xmlPath,
            'cdr_path'     => $r->cdrPath,
            'igv'          => $igv,
            'enviado_at'   => now(),
        ]);

        return $r;
    }

    /** Emite una Nota de Credito que anula/corrige una factura ya emitida. */
    public function anular(Factura $factura, string $codMotivo, string $desMotivo): ResultadoEmision
    {
        return $this->emisor()->emitirNotaCredito($factura, $codMotivo, $desMotivo);
    }

    /** Prueba la conexion contra SUNAT con las credenciales guardadas. */
    public function probarConexion(): ResultadoEmision
    {
        return $this->emisor()->probarConexion();
    }

    /* --------------------------------------------------------------------- */
    /* Representacion impresa                                                  */
    /* --------------------------------------------------------------------- */

    /**
     * Cadena para el codigo QR de la representacion impresa (SUNAT):
     * RUC|Tipo|Serie|Correlativo|IGV|Total|Fecha|TipoDocCliente|NumDocCliente|Hash
     */
    public function qrData(Factura $factura): ?string
    {
        if (! $factura->comprobante()) {
            return null;
        }

        [$tipoCliente, $numCliente] = $this->docCliente($factura);

        return implode('|', [
            $this->get('ruc'),
            $factura->tipo_comprobante,
            $factura->serie,
            $factura->correlativo,
            number_format((float) $factura->igv, 2, '.', ''),
            number_format((float) $factura->total, 2, '.', ''),
            optional($factura->fecha)->format('Y-m-d'),
            $tipoCliente,
            $numCliente,
            $factura->sunat_hash ?? '',
        ]);
    }

    /** Tipo y numero de documento del cliente segun el comprobante. */
    private function docCliente(Factura $factura): array
    {
        $doc = preg_replace('/\D/', '', (string) $factura->paciente?->ci);

        if (($factura->tipo_comprobante ?: '01') === '01') {
            return ['6', strlen($doc) === 11 ? $doc : '20000000001'];
        }

        return strlen($doc) === 8 ? ['1', $doc] : ['0', $doc ?: '00000000'];
    }
}
