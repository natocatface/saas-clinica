<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tablas = [
        'especialidades', 'medicos', 'pacientes', 'citas',
        'historias_clinicas', 'recetas', 'facturas', 'ordenes_laboratorio', 'productos',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $t) {
            Schema::table($t, fn (Blueprint $table) => $table->softDeletes());
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $t) {
            Schema::table($t, fn (Blueprint $table) => $table->dropSoftDeletes());
        }
    }
};
