<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public const ROLES = [
        'super_admin' => 'Super Admin',
        'admin' => 'Administrador',
        'medico' => 'Medico',
        'recepcion' => 'Recepcion',
        'enfermeria' => 'Enfermeria',
    ];

    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $clinicaId = $request->string('clinica')->toString();

        $usuarios = User::query()
            ->with('clinica')
            ->when($clinicaId, fn ($query) => $query->where('clinica_id', $clinicaId))
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(12)->withQueryString();

        return view('superadmin.usuarios.index', [
            'usuarios' => $usuarios, 'q' => $q, 'clinicaId' => $clinicaId,
            'clinicas' => Clinica::orderBy('nombre')->get(), 'roles' => self::ROLES,
        ]);
    }

    public function create()
    {
        return view('superadmin.usuarios.create', $this->formData(new User()));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'clinica_id' => ['nullable', 'exists:clinicas,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['password'] = Hash::make($data['password']);
        $data['clinica_id'] = $data['role'] === 'super_admin' ? null : ($data['clinica_id'] ?? null);

        User::create($data);

        return redirect()->route('superadmin.usuarios.index')->with('ok', 'Usuario creado.');
    }

    public function edit(User $usuario)
    {
        return view('superadmin.usuarios.edit', $this->formData($usuario));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'clinica_id' => ['nullable', 'exists:clinicas,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['clinica_id'] = $data['role'] === 'super_admin' ? null : ($data['clinica_id'] ?? null);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);

        return redirect()->route('superadmin.usuarios.index')->with('ok', 'Usuario actualizado.');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($request->user()->id === $usuario->id) {
            return back()->with('ok', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('superadmin.usuarios.index')->with('ok', 'Usuario eliminado.');
    }

    private function formData(User $usuario): array
    {
        return [
            'usuario' => $usuario,
            'clinicas' => Clinica::orderBy('nombre')->get(),
            'roles' => self::ROLES,
        ];
    }
}
