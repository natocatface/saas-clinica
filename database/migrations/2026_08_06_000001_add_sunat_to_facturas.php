<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos de Facturacion Electronica (SUNAT - Peru) para la tabla facturas.
 * Guarda el tipo de comprobante, serie/correlativo, el estado del envio a
 * SUNAT y las rutas del XML firmado y del CDR de respuesta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            // Comprobante electronico
            $table->string('tipo_comprobante', 2)->nullable()->after('numero'); // 01=Factura, 03=Boleta
            $table->string('serie', 8)->nullable()->after('tipo_comprobante');   // F001 / B001
            $table->string('correlativo', 12)->nullable()->after('serie');       // 1, 2, 3...
            $table->string('moneda_iso', 3)->default('PEN')->after('correlativo');
            $table->decimal('igv', 10, 2)->default(0)->after('total');

            // Estado de envio a SUNAT
            $table->string('sunat_estado', 20)->default('no_enviado')->after('estado');
            // no_enviado | pendiente | aceptado | rechazado | observado | anulado
            $table->string('sunat_hash')->nullable()->after('sunat_estado');
            $table->string('sunat_codigo')->nullable()->after('sunat_hash');   // codigo de respuesta CDR
            $table->text('sunat_mensaje')->nullable()->after('sunat_codigo');  // descripcion CDR / error
            $table->string('sunat_ticket')->nullable()->after('sunat_mensaje'); // baja / resumen
            $table->string('xml_path')->nullable()->after('sunat_ticket');
            $table->string('cdr_path')->nullable()->after('xml_path');
            $table->timestamp('enviado_at')->nullable()->after('cdr_path');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_comprobante', 'serie', 'correlativo', 'moneda_iso', 'igv',
                'sunat_estado', 'sunat_hash', 'sunat_codigo', 'sunat_mensaje',
                'sunat_ticket', 'xml_path', 'cdr_path', 'enviado_at',
            ]);
        });
    }
};
