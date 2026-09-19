<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    use HasFactory, BelongsToClinica, SoftDeletes, Auditable;

    protected $table = 'especialidades';

    protected $fillable = [
        'clinica_id','nombre', 'descripcion', 'tarifa', 'color', 'activo'];

    protected function casts(): array
    {
        return [
            'tarifa' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class);
    }
}
