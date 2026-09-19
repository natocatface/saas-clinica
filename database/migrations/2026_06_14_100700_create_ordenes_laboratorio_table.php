<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_laboratorio', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('medicos')->nullOnDelete();
            $table->date('fecha');
            $table->enum('estado', ['solicitada', 'en_proceso', 'completada', 'entregada'])->default('solicitada');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('laboratorio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_laboratorio')->cascadeOnDelete();
            $table->string('examen');
            $table->string('resultado')->nullable();
            $table->string('unidad')->nullable();
            $table->string('valor_referencia')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratorio_items');
        Schema::dropIfExists('ordenes_laboratorio');
    }
};
