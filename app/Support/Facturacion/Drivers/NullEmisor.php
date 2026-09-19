<?php

namespace App\Support\Facturacion\Drivers;

use App\Models\Factura;
use App\Support\Facturacion\Contracts\EmisorSunat;
use App\Support\Facturacion\ResultadoEmision;

/**
 * Driver "Ninguno": no envia nada a SUNAT, solo deja el comprobante en
 * estado pendiente. Util cuando aun no se cuenta con certificado digital.
 */
class NullEmisor implements EmisorSunat
{
    public function emitir(Factura $factura): ResultadoEmision
    {
        return ResultadoEmision::ok(
            estado: 'pendiente',
            codigo: null,
            mensaje: 'Comprobante generado en modo local. No se envio a SUNAT (driver "Ninguno").',
            hash: null,
            xmlPath: null,
            cdrPath: null,
        );
    }

    public function emitirNotaCredito(Factura $factura, string $codMotivo, string $desMotivo): ResultadoEmision
    {
        return ResultadoEmision::error(
            'El driver de emision es "Ninguno". Selecciona Greenter para emitir notas de credito.',
            estado: 'pendiente',
        );
    }

    public function probarConexion(): ResultadoEmision
    {
        return ResultadoEmision::error(
            'El driver de emision es "Ninguno". Selecciona Greenter para conectar con SUNAT.',
            estado: 'pendiente',
        );
    }
}
