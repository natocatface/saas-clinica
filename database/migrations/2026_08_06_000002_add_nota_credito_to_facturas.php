<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos de Nota de Credito (anulacion / correccion) para la tabla facturas.
 * La NC es un comprobante electronico (tipo 07) que hace referencia al
 * comprobante afectado (factura/boleta ya emitida).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->string('nc_serie', 8)->nullable()->after('enviado_at');
            $table->string('nc_correlativo', 12)->nullable()->after('nc_serie');
            $table->string('nc_estado', 20)->nullable()->after('nc_correlativo'); // pendiente|aceptado|rechazado|observado
            $table->string('nc_codigo')->nullable()->after('nc_estado');
            $table->text('nc_mensaje')->nullable()->after('nc_codigo');
            $table->string('nc_motivo', 2)->nullable()->after('nc_mensaje');       // catalogo 09
            $table->string('nc_motivo_desc')->nullable()->after('nc_motivo');
            $table->string('nc_xml_path')->nullable()->after('nc_motivo_desc');
            $table->string('nc_cdr_path')->nullable()->after('nc_xml_path');
            $table->timestamp('anulado_at')->nullable()->after('nc_cdr_path');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn([
                'nc_serie', 'nc_correlativo', 'nc_estado', 'nc_codigo', 'nc_mensaje',
                'nc_motivo', 'nc_motivo_desc', 'nc_xml_path', 'nc_cdr_path', 'anulado_at',
            ]);
        });
    }
};
