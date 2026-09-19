<?php

/*
|--------------------------------------------------------------------------
| Facturacion Electronica - SUNAT (Peru)
|--------------------------------------------------------------------------
| Catalogos y valores por defecto para la emision de comprobantes de pago
| electronicos (Boletas, Facturas y Notas de Credito) bajo el estandar
| UBL 2.1. Los valores especificos de cada clinica (RUC, credenciales,
| certificado, etc.) se guardan en la tabla `configuraciones` usando las
| claves del arreglo 'claves' y se editan desde el modulo de configuracion.
*/

return [

    // Driver de emision usado por defecto en instalaciones nuevas.
    'driver_default' => 'none',

    // Drivers disponibles para el select "Driver de emision".
    'drivers' => [
        'none'     => 'Ninguno (no emite, deja pendiente)',
        'greenter' => 'Greenter (firma y envia a SUNAT)',
    ],

    // Entornos de SUNAT.
    'entornos' => [
        'beta'       => 'Beta (homologacion / pruebas)',
        'produccion' => 'Produccion (emision real)',
    ],

    // Tipos de comprobante (catalogo 01 SUNAT).
    'tipos_comprobante' => [
        '01' => 'Factura',
        '03' => 'Boleta de venta',
    ],

    // Series sugeridas por tipo de comprobante.
    'series' => [
        '01' => 'F001', // Factura
        '03' => 'B001', // Boleta
    ],

    // Series sugeridas para Notas de Credito (tipo 07) segun documento afectado.
    'series_nc' => [
        '01' => 'FC01', // NC de factura
        '03' => 'BC01', // NC de boleta
    ],

    // Motivos de Nota de Credito (catalogo 09 de SUNAT).
    'motivos_nc' => [
        '01' => 'Anulacion de la operacion',
        '02' => 'Anulacion por error en el RUC',
        '03' => 'Correccion por error en la descripcion',
        '04' => 'Descuento global',
        '05' => 'Descuento por item',
        '06' => 'Devolucion total',
        '07' => 'Devolucion por item',
        '08' => 'Bonificacion',
        '09' => 'Disminucion en el valor',
        '10' => 'Otros conceptos',
    ],

    // Porcentaje de IGV vigente en Peru.
    'igv_porcentaje' => 18,

    // Datos de prueba validos en el entorno beta de SUNAT.
    'beta' => [
        'ruc'      => '20000000001',
        'usuario'  => 'MODDATOS',
        'clave'    => 'MODDATOS',
    ],

    // Claves con las que se guardan los datos en la tabla configuraciones.
    'claves' => [
        'habilitado', 'auto_emitir', 'driver', 'entorno',
        'ruc', 'razon_social', 'nombre_comercial', 'direccion',
        'ubigeo', 'departamento', 'provincia', 'distrito',
        'sol_usuario', 'sol_clave', 'cert_ruta',
        'serie_factura', 'serie_boleta', 'tipo_default',
        'precios_incluyen_igv',
        'serie_nc_factura', 'serie_nc_boleta',
    ],

    // Carpeta (relativa a storage/app) donde se guardan XML firmados y CDR.
    'almacen' => 'facturacion/pe',
];
