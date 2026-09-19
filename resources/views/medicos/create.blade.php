@extends('layouts.app')
@section('title', 'Nuevo medico')

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('medicos.index') }}" class="hover:text-brand-600">Medicos</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">Nuevo</span>
    </nav>
    <h1 class="text-2xl font-extrabold text-slate-800 mb-6">Nuevo medico</h1>
    @include('partials.flash')
    <div class="max-w-3xl rounded-3xl bg-white shadow-card p-6 sm:p-8">
        @include('medicos._form')
    </div>
@endsection
