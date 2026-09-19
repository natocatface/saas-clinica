<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use HasFactory, BelongsToClinica, SoftDeletes, Auditable;

    protected $table = 'citas';

    protected $fillable = [
        'clinica_id',
        'paciente_id', 'medico_id', 'especialidad_id',
        'fecha', 'hora', 'motivo', 'estado', 'notas',
        'recordatorio_enviado', 'recordatorio_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'recordatorio_enviado' => 'boolean',
            'recordatorio_at' => 'datetime',
        ];
    }

    public const ESTADOS = [
        'programada' => 'Programada',
        'confirmada' => 'Confirmada',
        'en_espera'  => 'En espera',
        'atendida'   => 'Atendida',
        'cancelada'  => 'Cancelada',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function estadoLabel(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }
}
