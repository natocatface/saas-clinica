@extends('layouts.superadmin')
@section('title', 'Editar plan')
@section('content')
    <nav class="text-sm text-slate-400 mb-5"><a href="{{ route('superadmin.planes.index') }}" class="hover:text-iris-600">Planes</a> <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">{{ $plan->nombre }}</span></nav>
    @include('partials.flash')
    <div class="max-w-3xl rounded-3xl bg-white shadow-card p-6 sm:p-8">@include('superadmin.planes._form')</div>
@endsection
