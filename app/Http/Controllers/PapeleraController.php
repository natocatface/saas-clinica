<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Factura;
use App\Models\HistoriaClinica;
use App\Models\Medico;
use App\Models\OrdenLaboratorio;
use App\Models\Paciente;
use App\Models\Producto;
use App\Models\Receta;
use Illuminate\Http\Request;

class PapeleraController extends Controller
{
    /** Entidades con papelera disponible. */
    public static function tipos(): array
    {
        return [
            'pacientes' => ['model' => Paciente::class, 'label' => 'Pacientes'],
            'citas' => ['model' => Cita::class, 'label' => 'Citas'],
            'medicos' => ['model' => Medico::class, 'label' => 'Medicos'],
            'especialidades' => ['model' => Especialidad::class, 'label' => 'Especialidades'],
            'historias' => ['model' => HistoriaClinica::class, 'label' => 'Historias'],
            'recetas' => ['model' => Receta::class, 'label' => 'Recetas'],
            'facturas' => ['model' => Factura::class, 'label' => 'Facturas'],
            'laboratorio' => ['model' => OrdenLaboratorio::class, 'label' => 'Laboratorio'],
            'productos' => ['model' => Producto::class, 'label' => 'Productos'],
        ];
    }

    private function modelo(string $tipo): string
    {
        $tipos = self::tipos();
        abort_unless(isset($tipos[$tipo]), 404);

        return $tipos[$tipo]['model'];
    }

    public function index(Request $request)
    {
        $tipos = self::tipos();
        $tipo = $request->string('tipo')->toString() ?: 'pacientes';
        abort_unless(isset($tipos[$tipo]), 404);

        $model = $tipos[$tipo]['model'];

        $registros = $model::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($m) => [
                'id' => $m->id,
                'descripcion' => $this->descripcion($tipo, $m),
                'eliminado' => $m->deleted_at,
            ]);

        // Conteos por tipo para las pestanas
        $conteos = [];
        foreach ($tipos as $k => $t) {
            $conteos[$k] = $t['model']::onlyTrashed()->count();
        }

        return view('papelera.index', compact('registros', 'tipo', 'tipos', 'conteos'));
    }

    public function restaurar(string $tipo, int $id)
    {
        $model = $this->modelo($tipo);
        $model::onlyTrashed()->findOrFail($id)->restore();

        return back()->with('ok', 'Registro restaurado correctamente.');
    }

    public function forzar(string $tipo, int $id)
    {
        $model = $this->modelo($tipo);
        $model::onlyTrashed()->findOrFail($id)->forceDelete();

        return back()->with('ok', 'Registro eliminado definitivamente.');
    }

    private function descripcion(string $tipo, $m): string
    {
        return match ($tipo) {
            'pacientes', 'medicos' => $m->nombre_completo,
            'especialidades', 'productos' => $m->nombre,
            'facturas', 'laboratorio' => $m->numero,
            'citas' => ($m->paciente?->nombre_completo ?? 'Cita').' · '.optional($m->fecha)->format('d/m/Y'),
            'historias', 'recetas' => ($m->paciente?->nombre_completo ?? '—').' · '.optional($m->fecha)->format('d/m/Y'),
            default => '#'.$m->id,
        };
    }
}
