<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'planes';

    protected $fillable = [
        'nombre', 'descripcion', 'precio_mensual', 'max_usuarios',
        'max_pacientes', 'caracteristicas', 'destacado', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio_mensual' => 'decimal:2',
            'destacado' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function clinicas(): HasMany
    {
        return $this->hasMany(Clinica::class);
    }

    public function caracteristicasLista(): array
    {
        return array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $this->caracteristicas)));
    }
}
