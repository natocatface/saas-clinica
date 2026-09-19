<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistoriaClinica extends Model
{
    use HasFactory, BelongsToClinica, SoftDeletes, Auditable;

    protected $table = 'historias_clinicas';

    protected $fillable = [
        'clinica_id',
        'paciente_id', 'medico_id', 'fecha', 'motivo_consulta', 'sintomas',
        'diagnostico', 'tratamiento', 'observaciones',
        'peso', 'talla', 'presion_arterial', 'temperatura', 'frecuencia_cardiaca',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(HistoriaArchivo::class);
    }

    public function getImcAttribute(): ?float
    {
        if ($this->peso && $this->talla) {
            $m = $this->talla / 100;
            return $m > 0 ? round($this->peso / ($m * $m), 1) : null;
        }
        return null;
    }
}
