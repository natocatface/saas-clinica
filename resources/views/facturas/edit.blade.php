@extends('layouts.app')
@section('title', 'Editar factura')

@section('content')
    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('facturas.index') }}" class="hover:text-brand-600">Facturacion</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">{{ $factura->numero }}</span>
    </nav>
    <h1 class="text-2xl font-extrabold text-slate-800 mb-6">Editar factura {{ $factura->numero }}</h1>
    @include('partials.flash')
    <div class="max-w-5xl rounded-3xl bg-white shadow-card p-6 sm:p-8">
        @include('facturas._form')
    </div>
@endsection
