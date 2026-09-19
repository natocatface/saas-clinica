<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegistroController extends Controller
{
    public function show(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('public.registro', [
            'planes' => Plan::where('activo', true)->orderBy('precio_mensual')->get(),
            'planSeleccionado' => $request->integer('plan') ?: null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clinica_nombre' => ['required', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'plan_id' => ['nullable', 'exists:planes,id'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'confirmed', Password::min(6)],
        ], [], [
            'clinica_nombre' => 'nombre de la clinica',
            'admin_email' => 'correo',
            'admin_password' => 'contrasena',
        ]);

        $user = DB::transaction(function () use ($data) {
            $plan = $data['plan_id'] ? Plan::find($data['plan_id']) : null;

            $clinica = Clinica::create([
                'nombre' => $data['clinica_nombre'],
                'slug' => Str::slug($data['clinica_nombre']).'-'.Str::lower(Str::random(4)),
                'ciudad' => $data['ciudad'] ?? null,
                'color' => '#7c44ff',
                'plan_id' => $plan?->id,
                'estado' => 'prueba',
                'fecha_inicio' => now()->toDateString(),
                'trial_ends_at' => now()->addDays(14)->toDateString(),
            ]);

            if ($plan) {
                Suscripcion::create([
                    'clinica_id' => $clinica->id, 'plan_id' => $plan->id, 'estado' => 'activa',
                    'monto' => $plan->precio_mensual, 'ciclo' => 'mensual',
                    'inicio' => now()->toDateString(), 'fin' => now()->addMonth()->toDateString(),
                ]);
            }

            return User::create([
                'clinica_id' => $clinica->id,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        });

        Auth::login($user);

        return redirect()->route('dashboard')->with('ok', 'Bienvenido. Tu clinica fue creada con 14 dias de prueba.');
    }
}
