<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public const ROLES = [
        'admin' => 'Administrador',
        'medico' => 'Medico',
        'recepcion' => 'Recepcion',
        'enfermeria' => 'Enfermeria',
    ];

    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $usuarios = User::query()
            ->where('clinica_id', $request->user()->clinica_id)
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('usuarios.index', ['usuarios' => $usuarios, 'q' => $q, 'roles' => self::ROLES]);
    }

    public function create()
    {
        return view('usuarios.create', ['usuario' => new User(), 'roles' => self::ROLES]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['password'] = Hash::make($data['password']);
        $data['clinica_id'] = $request->user()->clinica_id;

        User::create($data);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario creado correctamente.');
    }

    public function edit(Request $request, User $usuario)
    {
        abort_unless($usuario->clinica_id === $request->user()->clinica_id, 403);

        return view('usuarios.edit', ['usuario' => $usuario, 'roles' => self::ROLES]);
    }

    public function update(Request $request, User $usuario)
    {
        abort_unless($usuario->clinica_id === $request->user()->clinica_id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('ok', 'Usuario actualizado.');
    }

    public function destroy(Request $request, User $usuario)
    {
        abort_unless($usuario->clinica_id === $request->user()->clinica_id, 403);

        if ($request->user()->id === $usuario->id) {
            return redirect()->route('usuarios.index')->with('ok', 'No puedes eliminar tu propia cuenta.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('ok', 'Usuario eliminado.');
    }
}
