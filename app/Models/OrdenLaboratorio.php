<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenLaboratorio extends Model
{
    use HasFactory, BelongsToClinica, SoftDeletes, Auditable;

    protected $table = 'ordenes_laboratorio';

    protected $fillable = [
        'clinica_id','numero', 'paciente_id', 'medico_id', 'fecha', 'estado', 'observaciones'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public const ESTADOS = [
        'solicitada' => 'Solicitada',
        'en_proceso' => 'En proceso',
        'completada' => 'Completada',
        'entregada' => 'Entregada',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LaboratorioItem::class, 'orden_id');
    }

    public function estadoLabel(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public static function siguienteNumero(): string
    {
        $id = (int) (static::withoutGlobalScopes()->max('id') ?? 0) + 1;
        return 'LAB-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }
}
