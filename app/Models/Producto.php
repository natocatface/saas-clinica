<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory, BelongsToClinica, SoftDeletes, Auditable;

    protected $table = 'productos';

    protected $fillable = [
        'clinica_id',
        'nombre', 'codigo', 'categoria', 'descripcion', 'unidad',
        'stock', 'stock_minimo', 'precio_compra', 'precio_venta', 'vencimiento', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'vencimiento' => 'date',
            'activo' => 'boolean',
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
        ];
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function getPorVencerAttribute(): bool
    {
        return $this->vencimiento && $this->vencimiento->isBetween(now(), now()->addDays(30));
    }
}
