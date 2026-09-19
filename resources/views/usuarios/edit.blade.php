@extends('layouts.app')
@section('title', 'Editar usuario')

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('usuarios.index') }}" class="hover:text-brand-600">Usuarios</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">Editar</span>
    </nav>
    <h1 class="text-2xl font-extrabold text-slate-800 mb-6">Editar usuario</h1>
    @include('partials.flash')
    <div class="max-w-3xl rounded-3xl bg-white shadow-card p-6 sm:p-8">
        @include('usuarios._form')
    </div>
@endsection
