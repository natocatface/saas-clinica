<?php

namespace App\Support\Facturacion;

/**
 * Resultado de un intento de emision de comprobante ante SUNAT.
 * Es agnostico al driver (Greenter, simulado, etc.).
 */
class ResultadoEmision
{
    public function __construct(
        public bool $exito,
        public string $estado,          // aceptado | rechazado | observado | pendiente
        public ?string $codigo = null,  // codigo de respuesta del CDR
        public ?string $mensaje = null, // descripcion / mensaje de error
        public ?string $hash = null,    // digest del XML firmado
        public ?string $xmlPath = null, // ruta relativa (storage/app) del XML firmado
        public ?string $cdrPath = null, // ruta relativa (storage/app) del CDR (zip)
        public array $observaciones = [],
    ) {
    }

    public static function ok(string $estado, ?string $codigo, ?string $mensaje, ?string $hash, ?string $xmlPath, ?string $cdrPath, array $obs = []): self
    {
        return new self(true, $estado, $codigo, $mensaje, $hash, $xmlPath, $cdrPath, $obs);
    }

    public static function error(string $mensaje, ?string $codigo = null, string $estado = 'rechazado'): self
    {
        return new self(false, $estado, $codigo, $mensaje);
    }
}
