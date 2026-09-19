<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Plan;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClinicaController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $estado = $request->string('estado')->toString();

        $clinicas = Clinica::query()
            ->with('plan')->withCount('usuarios')
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->orderByDesc('id')
            ->paginate(10)->withQueryString();

        return view('superadmin.clinicas.index', [
            'clinicas' => $clinicas, 'q' => $q, 'estado' => $estado, 'estados' => Clinica::ESTADOS,
        ]);
    }

    public function create()
    {
        return view('superadmin.clinicas.create', [
            'clinica' => new Clinica(['estado' => 'prueba', 'color' => '#7c44ff']),
            'planes' => Plan::where('activo', true)->orderBy('precio_mensual')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'plan_id' => ['nullable', 'exists:planes,id'],
            'estado' => ['required', Rule::in(array_keys(Clinica::ESTADOS))],
            // Admin de la clinica
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:6'],
        ]);

        DB::transaction(function () use ($data) {
            $plan = $data['plan_id'] ? Plan::find($data['plan_id']) : null;

            $clinica = Clinica::create([
                'nombre' => $data['nombre'],
                'slug' => Str::slug($data['nombre']).'-'.Str::lower(Str::random(4)),
                'nit' => $data['nit'] ?? null,
                'email' => $data['email'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'ciudad' => $data['ciudad'] ?? null,
                'color' => $data['color'] ?? '#7c44ff',
                'plan_id' => $data['plan_id'] ?? null,
                'estado' => $data['estado'],
                'fecha_inicio' => now()->toDateString(),
                'trial_ends_at' => $data['estado'] === 'prueba' ? now()->addDays(14)->toDateString() : null,
            ]);

            // Usuario administrador de la clinica
            User::create([
                'clinica_id' => $clinica->id,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Suscripcion inicial si tiene plan
            if ($plan) {
                Suscripcion::create([
                    'clinica_id' => $clinica->id,
                    'plan_id' => $plan->id,
                    'estado' => 'activa',
                    'monto' => $plan->precio_mensual,
                    'ciclo' => 'mensual',
                    'inicio' => now()->toDateString(),
                    'fin' => now()->addMonth()->toDateString(),
                ]);
            }
        });

        return redirect()->route('superadmin.clinicas.index')->with('ok', 'Clinica creada con su administrador.');
    }

    public function show(Clinica $clinica)
    {
        $clinica->load(['plan', 'suscripciones.plan', 'usuarios']);

        return view('superadmin.clinicas.show', compact('clinica'));
    }

    public function edit(Clinica $clinica)
    {
        return view('superadmin.clinicas.edit', [
            'clinica' => $clinica,
            'planes' => Plan::where('activo', true)->orderBy('precio_mensual')->get(),
        ]);
    }

    public function update(Request $request, Clinica $clinica)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'nit' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'plan_id' => ['nullable', 'exists:planes,id'],
            'estado' => ['required', Rule::in(array_keys(Clinica::ESTADOS))],
        ]);

        $clinica->update($data);

        return redirect()->route('superadmin.clinicas.index')->with('ok', 'Clinica actualizada.');
    }

    public function destroy(Clinica $clinica)
    {
        $clinica->delete();

        return redirect()->route('superadmin.clinicas.index')->with('ok', 'Clinica eliminada.');
    }

    public function estado(Request $request, Clinica $clinica)
    {
        $data = $request->validate(['estado' => ['required', Rule::in(array_keys(Clinica::ESTADOS))]]);
        $clinica->update($data);

        return back()->with('ok', 'Estado de la clinica actualizado.');
    }
}
