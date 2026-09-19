<?php

namespace App\Models\Concerns;

use App\Models\Auditoria;

/**
 * Registra automaticamente en la bitacora (auditorias) las acciones
 * de creacion, edicion y eliminacion del modelo.
 */
trait Auditable
{
    protected static function bootAuditable(): void
    {
        foreach (['created' => 'crear', 'updated' => 'editar', 'deleted' => 'eliminar'] as $evento => $accion) {
            static::$evento(function ($model) use ($accion) {
                if (! auth()->check()) {
                    return;
                }

                Auditoria::withoutGlobalScopes()->create([
                    'clinica_id' => $model->clinica_id ?? auth()->user()->clinica_id,
                    'user_id' => auth()->id(),
                    'user_nombre' => auth()->user()->name,
                    'accion' => $accion,
                    'modelo' => class_basename($model),
                    'modelo_id' => $model->getKey(),
                    'descripcion' => method_exists($model, 'auditLabel') ? $model->auditLabel() : null,
                    'ip' => request()->ip(),
                ]);
            });
        }
    }
}
