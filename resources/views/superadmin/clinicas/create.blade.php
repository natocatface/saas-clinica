@extends('layouts.superadmin')
@section('title', 'Nueva clinica')

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('superadmin.clinicas.index') }}" class="hover:text-iris-600">Clinicas</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">Nueva</span>
    </nav>
    @include('partials.flash')
    <div class="max-w-4xl rounded-3xl bg-white shadow-card p-6 sm:p-8">
        @include('superadmin.clinicas._form')
    </div>
@endsection
