<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinica extends Model
{
    use HasFactory;

    protected $table = 'clinicas';

    protected $fillable = [
        'nombre', 'slug', 'nit', 'email', 'telefono', 'direccion', 'ciudad',
        'color', 'plan_id', 'estado', 'fecha_inicio', 'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'trial_ends_at' => 'date',
        ];
    }

    public const ESTADOS = [
        'prueba' => 'Prueba',
        'activa' => 'Activa',
        'suspendida' => 'Suspendida',
        'cancelada' => 'Cancelada',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function suscripciones(): HasMany
    {
        return $this->hasMany(Suscripcion::class);
    }

    public function estadoLabel(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }
}
