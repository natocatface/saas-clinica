<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tablas que pasan a ser multi-tenant. */
    private array $tablas = [
        'users', 'pacientes', 'medicos', 'especialidades', 'citas',
        'historias_clinicas', 'recetas', 'facturas', 'ordenes_laboratorio',
        'productos', 'configuraciones',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->foreignId('clinica_id')->nullable()->after('id')
                    ->constrained('clinicas')->nullOnDelete();
            });
        }

        // La configuracion deja de ser global: unica por clinica + clave
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->dropUnique('configuraciones_clave_unique');
            $table->unique(['clinica_id', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::table('configuraciones', function (Blueprint $table) {
            $table->dropUnique(['clinica_id', 'clave']);
            $table->unique('clave');
        });

        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropConstrainedForeignId('clinica_id');
            });
        }
    }
};
