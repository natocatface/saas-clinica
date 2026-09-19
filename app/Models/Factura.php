<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToClinica;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends Model
{
    use HasFactory, BelongsToClinica, SoftDeletes, Auditable;

    protected $table = 'facturas';

    protected $fillable = [
        'clinica_id',
        'numero', 'paciente_id', 'cita_id', 'fecha',
        'subtotal', 'descuento', 'total', 'estado', 'metodo_pago', 'notas',
        // Facturacion electronica (SUNAT - Peru)
        'tipo_comprobante', 'serie', 'correlativo', 'moneda_iso', 'igv',
        'sunat_estado', 'sunat_hash', 'sunat_codigo', 'sunat_mensaje',
        'sunat_ticket', 'xml_path', 'cdr_path', 'enviado_at',
        // Nota de credito (anulacion)
        'nc_serie', 'nc_correlativo', 'nc_estado', 'nc_codigo', 'nc_mensaje',
        'nc_motivo', 'nc_motivo_desc', 'nc_xml_path', 'nc_cdr_path', 'anulado_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'subtotal' => 'decimal:2',
            'descuento' => 'decimal:2',
            'total' => 'decimal:2',
            'igv' => 'decimal:2',
            'enviado_at' => 'datetime',
            'anulado_at' => 'datetime',
        ];
    }

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'pagada' => 'Pagada',
        'anulada' => 'Anulada',
    ];

    /** Etiquetas de estado del envio a SUNAT. */
    public const SUNAT_ESTADOS = [
        'no_enviado' => 'No enviado',
        'pendiente'  => 'Pendiente',
        'aceptado'   => 'Aceptado',
        'observado'  => 'Observado',
        'rechazado'  => 'Rechazado',
        'anulado'    => 'Anulado',
    ];

    public function sunatEstadoLabel(): string
    {
        return self::SUNAT_ESTADOS[$this->sunat_estado] ?? 'No enviado';
    }

    public function tipoComprobanteLabel(): string
    {
        return config('facturacion.tipos_comprobante')[$this->tipo_comprobante] ?? '—';
    }

    /** Nombre completo del comprobante electronico. Ej: F001-00000012 */
    public function comprobante(): ?string
    {
        if (! $this->serie || ! $this->correlativo) {
            return null;
        }

        return $this->serie.'-'.str_pad((string) $this->correlativo, 8, '0', STR_PAD_LEFT);
    }

    /** Siguiente correlativo para una serie (sin ceros a la izquierda). */
    public static function siguienteCorrelativo(string $serie): string
    {
        $ultimo = (int) static::withoutGlobalScopes()
            ->where('serie', $serie)
            ->max(\Illuminate\Support\Facades\DB::raw('CAST(correlativo AS UNSIGNED)'));

        return (string) ($ultimo + 1);
    }

    /** Siguiente correlativo para una serie de Nota de Credito. */
    public static function siguienteCorrelativoNota(string $serie): string
    {
        $ultimo = (int) static::withoutGlobalScopes()
            ->where('nc_serie', $serie)
            ->max(\Illuminate\Support\Facades\DB::raw('CAST(nc_correlativo AS UNSIGNED)'));

        return (string) ($ultimo + 1);
    }

    /** Nombre completo de la Nota de Credito. Ej: FC01-00000003 */
    public function notaCredito(): ?string
    {
        if (! $this->nc_serie || ! $this->nc_correlativo) {
            return null;
        }

        return $this->nc_serie.'-'.str_pad((string) $this->nc_correlativo, 8, '0', STR_PAD_LEFT);
    }

    /** Si el comprobante fue anulado mediante Nota de Credito aceptada. */
    public function anulado(): bool
    {
        return $this->nc_estado === 'aceptado' || $this->sunat_estado === 'anulado';
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(FacturaItem::class);
    }

    public function estadoLabel(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst($this->estado);
    }

    public static function siguienteNumero(): string
    {
        $id = (int) (static::withoutGlobalScopes()->max('id') ?? 0) + 1;
        return 'FAC-'.str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }
}
