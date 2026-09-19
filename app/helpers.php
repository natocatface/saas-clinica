<?php

use App\Models\Configuracion;

if (! function_exists('moneda')) {
    /** Simbolo de moneda configurado para la clinica actual. */
    function moneda(): string
    {
        try {
            return Configuracion::get('moneda') ?: 'Bs';
        } catch (\Throwable $e) {
            return 'Bs';
        }
    }
}

if (! function_exists('money')) {
    /** Formatea un monto con la moneda de la clinica. Ej: "Bs 1,250.00" */
    function money($valor, int $decimales = 2): string
    {
        return moneda().' '.number_format((float) $valor, $decimales);
    }
}

if (! function_exists('csv_descarga')) {
    /** Genera una descarga CSV (compatible con Excel) a partir de cabeceras y filas. */
    function csv_descarga(string $nombre, array $cabeceras, iterable $filas)
    {
        return response()->streamDownload(function () use ($cabeceras, $filas) {
            $out = fopen('php://output', 'w');
            // BOM para que Excel reconozca UTF-8
            fwrite($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, $cabeceras);
            foreach ($filas as $fila) {
                fputcsv($out, $fila);
            }
            fclose($out);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
