@extends('layouts.app')
@section('title', 'Nueva receta')

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('recetas.index') }}" class="hover:text-brand-600">Recetas</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">Nueva</span>
    </nav>
    <h1 class="text-2xl font-extrabold text-slate-800 mb-6">Nueva receta</h1>
    @include('partials.flash')
    <div class="max-w-5xl rounded-3xl bg-white shadow-card p-6 sm:p-8">
        @include('recetas._form')
    </div>
@endsection
