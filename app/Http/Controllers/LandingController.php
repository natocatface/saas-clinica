<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return Auth::user()->isSuperAdmin()
                ? redirect()->route('superadmin.dashboard')
                : redirect()->route('dashboard');
        }

        $planes = Plan::where('activo', true)->orderBy('precio_mensual')->get();

        return view('public.landing', compact('planes'));
    }
}
