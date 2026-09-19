<?php

namespace App\Support\Facturacion\Contracts;

use App\Models\Factura;
use App\Support\Facturacion\ResultadoEmision;

/**
 * Contrato que deben cumplir los distintos drivers de emision
 * (Greenter, simulado, etc.). Permite intercambiar la implementacion
 * sin tocar el resto del sistema.
 */
interface EmisorSunat
{
    /** Firma y envia el comprobante a SUNAT, devolviendo el resultado. */
    public function emitir(Factura $factura): ResultadoEmision;

    /** Emite una Nota de Credito (tipo 07) que anula/corrige el comprobante. */
    public function emitirNotaCredito(Factura $factura, string $codMotivo, string $desMotivo): ResultadoEmision;

    /** Verifica la conexion / credenciales contra SUNAT. */
    public function probarConexion(): ResultadoEmision;
}
