<?php

namespace App\Support\Facturacion\Drivers;

use App\Models\Factura;
use App\Support\Facturacion\Contracts\EmisorSunat;
use App\Support\Facturacion\FacturacionElectronica;
use App\Support\Facturacion\ResultadoEmision;
use Illuminate\Support\Facades\Storage;

/**
 * Driver de emision real basado en la libreria Greenter (greenter/greenter).
 *
 * Requiere:
 *   composer require greenter/greenter
 * y un certificado digital en formato PEM (Clave publica + privada) cuya ruta
 * se configura en el modulo de Facturacion Electronica.
 *
 * Firma el comprobante en formato UBL 2.1, lo envia a SUNAT (beta o produccion)
 * y guarda el XML firmado + el CDR de respuesta en storage/app/facturacion/pe.
 */
class GreenterEmisor implements EmisorSunat
{
    public function __construct(private FacturacionElectronica $fe)
    {
    }

    public function emitir(Factura $factura): ResultadoEmision
    {
        if (! class_exists(\Greenter\See::class)) {
            return ResultadoEmision::error(
                'La libreria Greenter no esta instalada. Ejecuta: composer require greenter/greenter',
                estado: 'pendiente',
            );
        }

        $error = $this->validarConfig();
        if ($error) {
            return ResultadoEmision::error($error, estado: 'pendiente');
        }

        try {
            $see = $this->crearSee();
            $invoice = $this->construirComprobante($factura);

            $signed = $see->getXmlSigned($invoice);
            $result = $see->send($invoice);

            // Guardar XML firmado
            $base = $this->nombreArchivo($invoice);
            $carpeta = config('facturacion.almacen');
            $xmlPath = "$carpeta/{$base}.xml";
            Storage::put($xmlPath, $signed);

            $hash = $this->extraerHash($signed);

            if (! $result->isSuccess()) {
                $err = $result->getError();

                return new ResultadoEmision(
                    exito: false,
                    estado: 'rechazado',
                    codigo: $err?->getCode(),
                    mensaje: $err?->getMessage() ?: 'SUNAT rechazo el comprobante.',
                    hash: $hash,
                    xmlPath: $xmlPath,
                );
            }

            // CDR de respuesta
            $cdr = $result->getCdrResponse();
            $cdrPath = "$carpeta/R-{$base}.zip";
            Storage::put($cdrPath, $result->getCdrZip());

            $codigo = $cdr->getCode();
            $notas = $cdr->getNotes() ?? [];
            $estado = $codigo === '0'
                ? (count($notas) ? 'observado' : 'aceptado')
                : 'rechazado';

            return ResultadoEmision::ok(
                estado: $estado,
                codigo: $codigo,
                mensaje: $cdr->getDescription(),
                hash: $hash,
                xmlPath: $xmlPath,
                cdrPath: $cdrPath,
                obs: $notas,
            );
        } catch (\Throwable $e) {
            return ResultadoEmision::error('Error al emitir: '.$e->getMessage(), estado: 'pendiente');
        }
    }

    public function emitirNotaCredito(Factura $factura, string $codMotivo, string $desMotivo): ResultadoEmision
    {
        if (! class_exists(\Greenter\See::class)) {
            return ResultadoEmision::error(
                'La libreria Greenter no esta instalada. Ejecuta: composer require greenter/greenter',
                estado: 'pendiente',
            );
        }

        $error = $this->validarConfig();
        if ($error) {
            return ResultadoEmision::error($error, estado: 'pendiente');
        }

        try {
            $see = $this->crearSee();
            $note = $this->construirNota($factura, $codMotivo, $desMotivo);

            $signed = $see->getXmlSigned($note);
            $result = $see->send($note);

            $base = $this->nombreArchivo($note);
            $carpeta = config('facturacion.almacen');
            $xmlPath = "$carpeta/{$base}.xml";
            Storage::put($xmlPath, $signed);

            $hash = $this->extraerHash($signed);

            if (! $result->isSuccess()) {
                $err = $result->getError();

                return new ResultadoEmision(
                    exito: false,
                    estado: 'rechazado',
                    codigo: $err?->getCode(),
                    mensaje: $err?->getMessage() ?: 'SUNAT rechazo la nota de credito.',
                    hash: $hash,
                    xmlPath: $xmlPath,
                );
            }

            $cdr = $result->getCdrResponse();
            $cdrPath = "$carpeta/R-{$base}.zip";
            Storage::put($cdrPath, $result->getCdrZip());

            $notas = $cdr->getNotes() ?? [];
            $estado = $cdr->getCode() === '0'
                ? (count($notas) ? 'observado' : 'aceptado')
                : 'rechazado';

            return ResultadoEmision::ok(
                estado: $estado,
                codigo: $cdr->getCode(),
                mensaje: $cdr->getDescription(),
                hash: $hash,
                xmlPath: $xmlPath,
                cdrPath: $cdrPath,
                obs: $notas,
            );
        } catch (\Throwable $e) {
            return ResultadoEmision::error('Error al emitir la nota de credito: '.$e->getMessage(), estado: 'pendiente');
        }
    }

    public function probarConexion(): ResultadoEmision
    {
        if (! class_exists(\Greenter\See::class)) {
            return ResultadoEmision::error(
                'La libreria Greenter no esta instalada. Ejecuta: composer require greenter/greenter',
                estado: 'pendiente',
            );
        }

        $error = $this->validarConfig();
        if ($error) {
            return ResultadoEmision::error($error, estado: 'pendiente');
        }

        try {
            // Cargar el certificado valida su formato/vigencia.
            $this->crearSee();

            return ResultadoEmision::ok(
                estado: 'aceptado',
                codigo: '0',
                mensaje: 'Configuracion valida: certificado cargado y credenciales listas para el entorno '
                    .$this->fe->entorno().'.',
                hash: null,
                xmlPath: null,
                cdrPath: null,
            );
        } catch (\Throwable $e) {
            return ResultadoEmision::error('No se pudo preparar la conexion: '.$e->getMessage());
        }
    }

    /* --------------------------------------------------------------------- */
    /* Construccion Greenter                                                  */
    /* --------------------------------------------------------------------- */

    private function crearSee(): \Greenter\See
    {
        $see = new \Greenter\See();
        $see->setCertificate(file_get_contents($this->fe->certificadoRuta()));
        $see->setService(
            $this->fe->entorno() === 'produccion'
                ? \Greenter\Ws\Services\SunatEndpoints::FE_PRODUCCION
                : \Greenter\Ws\Services\SunatEndpoints::FE_BETA
        );
        $see->setClaveSOL(
            $this->fe->get('ruc'),
            $this->fe->get('sol_usuario'),
            $this->fe->get('sol_clave'),
        );

        return $see;
    }

    private function construirComprobante(Factura $factura): \Greenter\Model\Sale\Invoice
    {
        $tipo = $factura->tipo_comprobante ?: '01';
        $t = $this->detallesYTotales($factura);

        return (new \Greenter\Model\Sale\Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // Venta interna
            ->setTipoDoc($tipo)
            ->setSerie($factura->serie ?: $this->fe->serie($tipo))
            ->setCorrelativo((string) ($factura->correlativo ?: '1'))
            ->setFechaEmision(new \DateTime())
            ->setFormaPago(new \Greenter\Model\Sale\FormaPagoContado())
            ->setTipoMoneda('PEN')
            ->setCompany($this->company())
            ->setClient($this->construirCliente($factura, $tipo))
            ->setMtoOperGravadas($t['gravadas'])
            ->setMtoIGV($t['igv'])
            ->setTotalImpuestos($t['igv'])
            ->setValorVenta($t['gravadas'])
            ->setSubTotal($t['total'])
            ->setMtoImpVenta($t['total'])
            ->setDetails($t['details'])
            ->setLegends([$this->legendMonto($t['total'])]);
    }

    /** Construye la Nota de Credito (tipo 07) que anula/corrige el comprobante. */
    private function construirNota(Factura $factura, string $codMotivo, string $desMotivo): \Greenter\Model\Sale\Note
    {
        $tipoAfectado = $factura->tipo_comprobante ?: '01';
        $t = $this->detallesYTotales($factura);

        return (new \Greenter\Model\Sale\Note())
            ->setUblVersion('2.1')
            ->setTipoDoc('07') // Nota de Credito
            ->setSerie($factura->nc_serie)
            ->setCorrelativo((string) $factura->nc_correlativo)
            ->setFechaEmision(new \DateTime())
            ->setTipDocAfectado($tipoAfectado)
            ->setNumDocfectado($factura->serie.'-'.$factura->correlativo)
            ->setCodMotivo($codMotivo)
            ->setDesMotivo($desMotivo)
            ->setTipoMoneda('PEN')
            ->setCompany($this->company())
            ->setClient($this->construirCliente($factura, $tipoAfectado))
            ->setMtoOperGravadas($t['gravadas'])
            ->setMtoIGV($t['igv'])
            ->setTotalImpuestos($t['igv'])
            ->setMtoImpVenta($t['total'])
            ->setDetails($t['details'])
            ->setLegends([$this->legendMonto($t['total'])]);
    }

    /** Datos del emisor (empresa) tomados de la configuracion. */
    private function company(): \Greenter\Model\Company\Company
    {
        return (new \Greenter\Model\Company\Company())
            ->setRuc($this->fe->get('ruc'))
            ->setRazonSocial($this->fe->get('razon_social'))
            ->setNombreComercial($this->fe->get('nombre_comercial') ?: $this->fe->get('razon_social'))
            ->setAddress((new \Greenter\Model\Company\Address())
                ->setUbigueo($this->fe->get('ubigeo') ?: '150101')
                ->setDepartamento($this->fe->get('departamento') ?: 'LIMA')
                ->setProvincia($this->fe->get('provincia') ?: 'LIMA')
                ->setDistrito($this->fe->get('distrito') ?: 'LIMA')
                ->setUrbanizacion('-')
                ->setDireccion($this->fe->get('direccion') ?: '-')
                ->setCodLocal('0000'));
    }

    /**
     * Calcula los detalles (SaleDetail) y los totales reconciliados por linea.
     * @return array{details: array, gravadas: float, igv: float, total: float}
     */
    private function detallesYTotales(Factura $factura): array
    {
        $igvPct = (float) config('facturacion.igv_porcentaje', 18);
        $factor = 1 + $igvPct / 100;
        $incluyeIgvPrecio = $this->fe->preciosIncluyenIgv();

        $details = [];
        $gravadas = 0.0;
        $igvTotal = 0.0;

        foreach ($factura->items as $item) {
            $cantidad = (float) $item->cantidad ?: 1;
            $montoLinea = (float) $item->subtotal;

            if ($incluyeIgvPrecio) {
                // El importe de la linea YA incluye IGV: se extrae la base.
                $valorVenta = round($montoLinea / $factor, 2);
                $igvLinea = round($valorVenta * $igvPct / 100, 2);
                $valorUnitario = round($valorVenta / $cantidad, 2);
                $precioUnitario = round($montoLinea / $cantidad, 2);
            } else {
                // El importe de la linea es base imponible: el IGV se suma encima.
                $valorVenta = round($montoLinea, 2);
                $igvLinea = round($valorVenta * $igvPct / 100, 2);
                $valorUnitario = round($valorVenta / $cantidad, 2);
                $precioUnitario = round($valorUnitario * $factor, 2);
            }

            $gravadas += $valorVenta;
            $igvTotal += $igvLinea;

            $details[] = (new \Greenter\Model\Sale\SaleDetail())
                ->setCodProducto('SERV')
                ->setUnidad('ZZ') // Servicios
                ->setCantidad($cantidad)
                ->setDescripcion($item->descripcion ?: 'Servicio')
                ->setMtoBaseIgv($valorVenta)
                ->setPorcentajeIgv($igvPct)
                ->setIgv($igvLinea)
                ->setTipAfeIgv('10') // Gravado - Operacion Onerosa
                ->setTotalImpuestos($igvLinea)
                ->setMtoValorVenta($valorVenta)
                ->setMtoValorUnitario($valorUnitario)
                ->setMtoPrecioUnitario($precioUnitario);
        }

        $gravadas = round($gravadas, 2);
        $igvTotal = round($igvTotal, 2);

        return [
            'details'  => $details,
            'gravadas' => $gravadas,
            'igv'      => $igvTotal,
            'total'    => round($gravadas + $igvTotal, 2),
        ];
    }

    private function legendMonto(float $total): \Greenter\Model\Sale\Legend
    {
        return (new \Greenter\Model\Sale\Legend())
            ->setCode('1000')
            ->setValue($this->montoEnLetras($total).' SOLES');
    }

    private function construirCliente(Factura $factura, string $tipo): \Greenter\Model\Client\Client
    {
        $paciente = $factura->paciente;
        $nombre = $paciente?->nombre_completo ?: 'CLIENTE VARIOS';
        $doc = $paciente?->ci ?: null;

        // Factura (01) => cliente con RUC (6). Boleta (03) => DNI (1) o generico.
        if ($tipo === '01') {
            $tipoDoc = '6';
            $numDoc = $doc && strlen(preg_replace('/\D/', '', $doc)) === 11
                ? preg_replace('/\D/', '', $doc)
                : '20000000001';
        } else {
            $tipoDoc = '1';
            $numDoc = $doc ? preg_replace('/\D/', '', $doc) : '00000000';
            if (strlen($numDoc) !== 8) {
                $tipoDoc = '0'; // Otros / sin documento
                $numDoc = $numDoc ?: '00000000';
            }
        }

        return (new \Greenter\Model\Client\Client())
            ->setTipoDoc($tipoDoc)
            ->setNumDoc($numDoc)
            ->setRznSocial($nombre);
    }

    /* --------------------------------------------------------------------- */
    /* Utilidades                                                             */
    /* --------------------------------------------------------------------- */

    private function nombreArchivo($doc): string
    {
        return implode('-', [
            $this->fe->get('ruc'),
            $doc->getTipoDoc(),
            $doc->getSerie(),
            $doc->getCorrelativo(),
        ]);
    }

    private function extraerHash(string $xmlFirmado): ?string
    {
        if (class_exists(\Greenter\Report\XmlUtils::class)
            && method_exists(\Greenter\Report\XmlUtils::class, 'getHashSign')) {
            try {
                return \Greenter\Report\XmlUtils::getHashSign($xmlFirmado);
            } catch (\Throwable $e) {
                // continua
            }
        }

        return null;
    }

    private function validarConfig(): ?string
    {
        if (! $this->fe->get('ruc')) {
            return 'Falta el RUC del emisor en la configuracion.';
        }
        if (! $this->fe->get('sol_usuario') || ! $this->fe->get('sol_clave')) {
            return 'Faltan las credenciales de Clave SOL (usuario y clave).';
        }
        if (! $this->fe->certificadoExiste()) {
            return 'No se encontro el certificado digital (.pem) en la ruta configurada.';
        }

        return null;
    }

    /** Convierte un monto a letras en espanol para la leyenda del comprobante. */
    private function montoEnLetras(float $monto): string
    {
        $entero = (int) floor($monto);
        $centavos = (int) round(($monto - $entero) * 100);

        $texto = $entero === 0 ? 'CERO' : trim($this->numeroALetras($entero));

        return $texto.' CON '.str_pad((string) $centavos, 2, '0', STR_PAD_LEFT).'/100';
    }

    private function numeroALetras(int $n): string
    {
        $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
            'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE',
            'DIECIOCHO', 'DIECINUEVE', 'VEINTE'];
        $decenas = ['', '', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
            'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        if ($n <= 20) {
            return $unidades[$n];
        }
        if ($n < 30) {
            return 'VEINTI'.$unidades[$n - 20];
        }
        if ($n < 100) {
            $d = intdiv($n, 10);
            $u = $n % 10;

            return $decenas[$d].($u ? ' Y '.$unidades[$u] : '');
        }
        if ($n === 100) {
            return 'CIEN';
        }
        if ($n < 1000) {
            $c = intdiv($n, 100);
            $r = $n % 100;

            return $centenas[$c].($r ? ' '.$this->numeroALetras($r) : '');
        }
        if ($n < 1000000) {
            $miles = intdiv($n, 1000);
            $r = $n % 1000;
            $pref = $miles === 1 ? 'MIL' : $this->numeroALetras($miles).' MIL';

            return $pref.($r ? ' '.$this->numeroALetras($r) : '');
        }

        $millones = intdiv($n, 1000000);
        $r = $n % 1000000;
        $pref = $millones === 1 ? 'UN MILLON' : $this->numeroALetras($millones).' MILLONES';

        return $pref.($r ? ' '.$this->numeroALetras($r) : '');
    }
}
