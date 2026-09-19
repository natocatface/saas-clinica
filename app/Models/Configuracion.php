<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Configuracion extends Model
{
    use BelongsToClinica;

    protected $table = 'configuraciones';

    protected $fillable = ['clinica_id', 'clave', 'valor'];

    public $timestamps = true;

    private static function clinicaId(): ?int
    {
        return auth()->check() ? auth()->user()->clinica_id : null;
    }

    /** Obtiene un valor de configuracion de la clinica actual. */
    public static function get(string $clave, ?string $default = null): ?string
    {
        $cid = self::clinicaId();
        $all = Cache::rememberForever('config_'.($cid ?? 'global'), fn () => static::pluck('valor', 'clave')->toArray());

        return $all[$clave] ?? $default;
    }

    /** Guarda (o actualiza) un valor para la clinica actual. */
    public static function set(string $clave, ?string $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        Cache::forget('config_'.(self::clinicaId() ?? 'global'));
    }
}
