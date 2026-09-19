<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('medicos')->nullOnDelete();
            $table->date('fecha');
            $table->string('motivo_consulta')->nullable();
            $table->text('sintomas')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->text('observaciones')->nullable();
            // Signos vitales
            $table->decimal('peso', 5, 2)->nullable();        // kg
            $table->decimal('talla', 5, 2)->nullable();       // cm
            $table->string('presion_arterial', 20)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable(); // C
            $table->unsignedSmallInteger('frecuencia_cardiaca')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
