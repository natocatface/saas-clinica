<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $filtro = $request->string('filtro')->toString();

        $productos = Producto::query()
            ->when($q, function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%")
                    ->orWhere('categoria', 'like', "%{$q}%");
            })
            ->when($filtro === 'bajo', fn ($query) => $query->whereColumn('stock', '<=', 'stock_minimo'))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        $resumen = [
            'total' => Producto::count(),
            'bajo' => Producto::whereColumn('stock', '<=', 'stock_minimo')->count(),
            'valor' => Producto::sum(DB::raw('stock * precio_venta')),
        ];

        return view('farmacia.index', compact('productos', 'q', 'filtro', 'resumen'));
    }

    public function create()
    {
        return view('farmacia.create', ['producto' => new Producto()]);
    }

    public function store(Request $request)
    {
        Producto::create($this->validated($request));

        return redirect()->route('productos.index')->with('ok', 'Producto creado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['movimientos' => fn ($q) => $q->latest('fecha')->with('user')]);

        return view('farmacia.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        return view('farmacia.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $producto->update($this->validated($request));

        return redirect()->route('productos.index')->with('ok', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('ok', 'Producto eliminado.');
    }

    public function movimiento(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'tipo' => ['required', 'in:entrada,salida'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['tipo'] === 'salida' && $data['cantidad'] > $producto->stock) {
            return back()->withErrors(['cantidad' => 'No hay stock suficiente para la salida.']);
        }

        DB::transaction(function () use ($producto, $data, $request) {
            $producto->movimientos()->create([
                'user_id' => $request->user()->id,
                'tipo' => $data['tipo'],
                'cantidad' => $data['cantidad'],
                'motivo' => $data['motivo'] ?? null,
                'fecha' => now(),
            ]);

            $producto->stock += $data['tipo'] === 'entrada' ? $data['cantidad'] : -$data['cantidad'];
            $producto->save();
        });

        return back()->with('ok', 'Movimiento de stock registrado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:100'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string'],
            'unidad' => ['nullable', 'string', 'max:50'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'precio_compra' => ['nullable', 'numeric', 'min:0'],
            'precio_venta' => ['nullable', 'numeric', 'min:0'],
            'vencimiento' => ['nullable', 'date'],
            'activo' => ['nullable', 'boolean'],
        ]) + ['activo' => $request->boolean('activo')];
    }
}
