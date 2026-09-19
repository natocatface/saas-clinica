<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HistoriaArchivo extends Model
{
    protected $table = 'historia_archivos';

    protected $fillable = ['historia_clinica_id', 'nombre', 'ruta', 'mime', 'tamano'];

    public function historia(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class, 'historia_clinica_id');
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->ruta);
    }

    public function tamanoLegible(): string
    {
        $b = (int) $this->tamano;
        if ($b >= 1048576) return round($b / 1048576, 1).' MB';
        if ($b >= 1024) return round($b / 1024).' KB';
        return $b.' B';
    }
}
