@extends('layouts.superadmin')
@section('title', 'Nuevo usuario')
@section('content')
    <nav class="text-sm text-slate-400 mb-5"><a href="{{ route('superadmin.usuarios.index') }}" class="hover:text-iris-600">Usuarios</a> <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">Nuevo</span></nav>
    @include('partials.flash')
    <div class="max-w-3xl rounded-3xl bg-white shadow-card p-6 sm:p-8">@include('superadmin.usuarios._form')</div>
@endsection
