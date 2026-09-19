<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /** El super admin entra al panel de una clinica. */
    public function start(Request $request, Clinica $clinica)
    {
        abort_unless($request->user()?->isSuperAdmin(), 403);

        $destino = User::where('clinica_id', $clinica->id)
            ->where('is_active', true)
            ->orderByRaw("FIELD(role, 'admin') desc")
            ->first();

        if (! $destino) {
            return back()->with('ok', 'Esta clinica no tiene usuarios activos para impersonar.');
        }

        $request->session()->put('impersonator_id', $request->user()->id);
        Auth::login($destino);

        return redirect()->route('dashboard')->with('ok', 'Estas viendo el panel de '.$clinica->nombre.'.');
    }

    /** Vuelve a la sesion del super admin. */
    public function stop(Request $request)
    {
        $originalId = $request->session()->pull('impersonator_id');

        if (! $originalId) {
            return redirect()->route('dashboard');
        }

        $original = User::find($originalId);
        if ($original) {
            Auth::login($original);
        }

        return redirect()->route('superadmin.dashboard')->with('ok', 'Volviste a tu sesion de Super Admin.');
    }
}
