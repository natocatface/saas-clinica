<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaboratorioItem extends Model
{
    protected $table = 'laboratorio_items';

    protected $fillable = ['orden_id', 'examen', 'resultado', 'unidad', 'valor_referencia'];

    public function orden(): BelongsTo
    {
        return $this->belongsTo(OrdenLaboratorio::class, 'orden_id');
    }
}
