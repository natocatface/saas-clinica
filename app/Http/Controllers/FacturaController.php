<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Factura;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $estado = $request->string('estado')->toString();

        $facturas = Factura::query()
            ->with('paciente')
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->when($q, function ($query) use ($q) {
                $query->where('numero', 'like', "%{$q}%")
                    ->orWhereHas('paciente', function ($p) use ($q) {
                        $p->where('nombres', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%");
                    });
            })
            ->orderByDesc('fecha')->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'total' => Factura::where('estado', 'pagada')->sum('total'),
            'pendiente' => Factura::where('estado', 'pendiente')->sum('total'),
            'count' => Factura::count(),
        ];

        return view('facturas.index', [
            'facturas' => $facturas, 'q' => $q, 'estado' => $estado,
            'estados' => Factura::ESTADOS, 'resumen' => $resumen,
        ]);
    }

    public function create()
    {
        return view('facturas.create', $this->formData(new Factura()));
    }

    public function store(Request $request, \App\Support\Facturacion\FacturacionElectronica $fe)
    {
        $data = $this->validated($request);

        $factura = DB::transaction(function () use ($data) {
            $factura = Factura::create(array_merge($data['factura'], ['numero' => Factura::siguienteNumero()]));
            $factura->items()->createMany($data['items']);

            return $factura;
        });

        $msg = 'Factura creada correctamente.';

        // Emision automatica ante SUNAT si esta configurada.
        if ($fe->habilitado() && $fe->autoEmitir() && $fe->driver() !== 'none') {
            $r = $fe->emitirFactura($factura);
            $msg .= $r->exito
                ? ' Comprobante enviado a SUNAT: '.$r->mensaje
                : ' (No se pudo emitir automaticamente: '.$r->mensaje.')';
        }

        return redirect()->route('facturas.index')->with('ok', $msg);
    }

    public function show(Factura $factura, \App\Support\Facturacion\FacturacionElectronica $fe)
    {
        $factura->load(['paciente', 'items', 'cita']);

        return view('facturas.show', [
            'factura' => $factura,
            'qr' => $fe->qrData($factura),
        ]);
    }

    public function edit(Factura $factura)
    {
        $factura->load('items');

        return view('facturas.edit', $this->formData($factura));
    }

    public function update(Request $request, Factura $factura)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($factura, $data) {
            $factura->update($data['factura']);
            $factura->items()->delete();
            $factura->items()->createMany($data['items']);
        });

        return redirect()->route('facturas.index')->with('ok', 'Factura actualizada.');
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();

        return redirect()->route('facturas.index')->with('ok', 'Factura eliminada.');
    }

    public function pagar(Request $request, Factura $factura)
    {
        $factura->update([
            'estado' => 'pagada',
            'metodo_pago' => $request->string('metodo_pago')->toString() ?: 'efectivo',
        ]);

        return back()->with('ok', 'Factura marcada como pagada.');
    }

    private function formData(Factura $factura): array
    {
        return [
            'factura' => $factura,
            'pacientes' => Paciente::orderBy('apellidos')->get(),
            'citas' => Cita::with('paciente')->orderByDesc('fecha')->take(100)->get(),
            'estados' => Factura::ESTADOS,
        ];
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'cita_id' => ['nullable', 'exists:citas,id'],
            'fecha' => ['required', 'date'],
            'descuento' => ['nullable', 'numeric', 'min:0'],
            'estado' => ['required', 'in:'.implode(',', array_keys(Factura::ESTADOS))],
            'metodo_pago' => ['nullable', 'string', 'max:50'],
            'notas' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.descripcion' => ['nullable', 'string', 'max:255'],
            'items.*.cantidad' => ['nullable', 'numeric', 'min:0'],
            'items.*.precio_unitario' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = collect($validated['items'])
            ->filter(fn ($i) => ! empty($i['descripcion']))
            ->map(function ($i) {
                $cant = (float) ($i['cantidad'] ?? 1);
                $precio = (float) ($i['precio_unitario'] ?? 0);
                return [
                    'descripcion' => $i['descripcion'],
                    'cantidad' => $cant,
                    'precio_unitario' => $precio,
                    'subtotal' => round($cant * $precio, 2),
                ];
            })->values()->all();

        if (empty($items)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['items' => 'Agrega al menos un concepto.']);
        }

        $subtotal = array_sum(array_column($items, 'subtotal'));
        $descuento = (float) ($validated['descuento'] ?? 0);
        $total = max(0, $subtotal - $descuento);

        return [
            'factura' => [
                'paciente_id' => $validated['paciente_id'],
                'cita_id' => $validated['cita_id'] ?? null,
                'fecha' => $validated['fecha'],
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'estado' => $validated['estado'],
                'metodo_pago' => $validated['metodo_pago'] ?? null,
                'notas' => $validated['notas'] ?? null,
            ],
            'items' => $items,
        ];
    }
}
