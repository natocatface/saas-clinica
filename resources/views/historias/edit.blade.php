@extends('layouts.app')
@section('title', 'Editar atencion')

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('historias.index') }}" class="hover:text-brand-600">Historias Clinicas</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">Editar</span>
    </nav>
    <h1 class="text-2xl font-extrabold text-slate-800 mb-6">Editar atencion clinica</h1>
    @include('partials.flash')
    <div class="max-w-4xl rounded-3xl bg-white shadow-card p-6 sm:p-8">
        @include('historias._form')
    </div>
@endsection
