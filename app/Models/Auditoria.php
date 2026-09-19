<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    use BelongsToClinica;

    protected $table = 'auditorias';

    protected $fillable = [
        'clinica_id', 'user_id', 'user_nombre', 'accion',
        'modelo', 'modelo_id', 'descripcion', 'ip',
    ];

    public const ACCIONES = [
        'crear' => 'Creó',
        'editar' => 'Editó',
        'eliminar' => 'Eliminó',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function accionLabel(): string
    {
        return self::ACCIONES[$this->accion] ?? ucfirst($this->accion);
    }
}
