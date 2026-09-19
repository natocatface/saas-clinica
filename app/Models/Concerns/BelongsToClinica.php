<?php

namespace App\Models\Concerns;

use App\Models\Clinica;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Aisla los datos por clinica (multi-tenant).
 * - Aplica un global scope que filtra por la clinica del usuario autenticado.
 * - Asigna automaticamente clinica_id al crear registros.
 * El super admin (sin clinica_id) no es filtrado: ve todos los registros.
 */
trait BelongsToClinica
{
    protected static function bootBelongsToClinica(): void
    {
        static::addGlobalScope('clinica', function (Builder $builder) {
            $user = auth()->user();
            if ($user && $user->clinica_id) {
                $builder->where($builder->getModel()->getTable().'.clinica_id', $user->clinica_id);
            }
        });

        static::creating(function ($model) {
            $user = auth()->user();
            if (empty($model->clinica_id) && $user && $user->clinica_id) {
                $model->clinica_id = $user->clinica_id;
            }
        });
    }

    public function clinica(): BelongsTo
    {
        return $this->belongsTo(Clinica::class);
    }
}
