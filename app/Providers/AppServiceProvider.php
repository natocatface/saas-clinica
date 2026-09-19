<?php

namespace App\Providers;

use App\Models\Cita;
use App\Models\Producto;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('helpers.php');
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // Notificaciones (campana) para usuarios de una clinica
        View::composer('partials.topbar', function ($view) {
            $alertas = [];
            $user = auth()->user();

            if ($user && $user->clinica_id) {
                $citasHoy = Cita::whereDate('fecha', today())->where('estado', '!=', 'cancelada')->count();
                if ($citasHoy > 0) {
                    $alertas[] = [
                        'icon' => 'calendar', 'tone' => 'brand',
                        'texto' => $citasHoy.' cita(s) para hoy',
                        'url' => route('citas.calendario'),
                    ];
                }

                $recordatorios = Cita::whereDate('fecha', today()->addDay())
                    ->where('estado', '!=', 'cancelada')->where('recordatorio_enviado', false)->count();
                if ($recordatorios > 0) {
                    $alertas[] = [
                        'icon' => 'bell', 'tone' => 'amber',
                        'texto' => $recordatorios.' recordatorio(s) pendiente(s) para manana',
                        'url' => route('citas.index'),
                    ];
                }

                $stockBajo = 0;
                if (in_array($user->role, ['admin', 'enfermeria'], true)) {
                    try {
                        $stockBajo = Producto::whereColumn('stock', '<=', 'stock_minimo')->count();
                    } catch (\Throwable $e) {
                        $stockBajo = 0;
                    }
                }
                if ($stockBajo > 0) {
                    $alertas[] = [
                        'icon' => 'box', 'tone' => 'rose',
                        'texto' => $stockBajo.' producto(s) con stock bajo',
                        'url' => route('productos.index', ['filtro' => 'bajo']),
                    ];
                }
            }

            $view->with('notificaciones', $alertas);
        });
    }
}
