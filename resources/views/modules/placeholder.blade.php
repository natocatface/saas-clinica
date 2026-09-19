@extends('layouts.app')
@section('title', $title)

@php
    // Funcionalidades sugeridas por modulo (para la siguiente fase de desarrollo).
    $features = [
        'citas' => ['Calendario diario/semanal/mensual', 'Reserva y reprogramacion', 'Recordatorios automaticos', 'Confirmacion por estado', 'Asignacion por medico y consultorio'],
        'pacientes' => ['Ficha completa del paciente', 'Busqueda y filtros avanzados', 'Historial de citas', 'Datos de seguro y contacto', 'Documentos adjuntos'],
        'medicos' => ['Perfil del profesional', 'Horarios y disponibilidad', 'Asignacion de especialidades', 'Agenda individual', 'Comisiones y honorarios'],
        'historias' => ['Expediente clinico electronico', 'Evolucion y notas SOAP', 'Diagnosticos CIE-10', 'Signos vitales', 'Antecedentes y alergias'],
        'recetas' => ['Emision de recetas', 'Catalogo de medicamentos', 'Plantillas de prescripcion', 'Impresion y PDF', 'Historial por paciente'],
        'especialidades' => ['Catalogo de especialidades', 'Servicios y tarifas', 'Asignacion a medicos', 'Tiempos de atencion'],
        'laboratorio' => ['Ordenes de examenes', 'Captura de resultados', 'Rangos de referencia', 'Entrega de informes', 'Integracion con historia'],
        'farmacia' => ['Control de stock', 'Entradas y salidas', 'Lotes y vencimientos', 'Alertas de stock minimo', 'Proveedores'],
        'facturacion' => ['Emision de facturas', 'Cobros y pagos', 'Gestion de seguros', 'Estados de cuenta', 'Reportes de ingresos'],
        'reportes' => ['Indicadores clave (KPI)', 'Reportes financieros', 'Productividad por medico', 'Ocupacion y demanda', 'Exportacion a Excel/PDF'],
        'usuarios' => ['Gestion de usuarios', 'Roles y permisos', 'Auditoria de accesos', 'Estado activo/inactivo', 'Restablecer contrasenas'],
        'configuracion' => ['Datos de la clinica', 'Parametros generales', 'Consultorios y sedes', 'Personalizacion visual', 'Copias de seguridad'],
    ];
    $list = $features[$moduleKey] ?? ['Listado de registros', 'Crear', 'Editar', 'Eliminar', 'Busqueda y filtros'];
@endphp

@section('content')

    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('dashboard') }}" class="hover:text-brand-600">Inicio</a>
        <span class="mx-1.5">/</span>
        <span class="text-slate-600 font-medium">{{ $title }}</span>
    </nav>

    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center shadow-lg shadow-brand-500/30">
                @include('partials.icon', ['name' => $icon, 'class' => 'w-6 h-6'])
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">{{ $title }}</h1>
                <p class="text-sm text-slate-500">{{ $description }}</p>
            </div>
        </div>
        <button class="px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 flex items-center gap-2">
            @include('partials.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            Nuevo registro
        </button>
    </div>

    <div class="rounded-3xl bg-white shadow-card p-10 text-center">
        <div class="mx-auto w-20 h-20 rounded-3xl bg-brand-50 text-brand-500 grid place-items-center mb-5">
            @include('partials.icon', ['name' => $icon, 'class' => 'w-10 h-10'])
        </div>
        <h2 class="text-xl font-bold text-slate-800">Modulo en construccion</h2>
        <p class="mt-2 text-slate-500 max-w-lg mx-auto">
            La interfaz de <span class="font-semibold text-slate-700">{{ $title }}</span> esta lista para conectarse.
            En la siguiente fase se implementara el CRUD completo y la logica de negocio.
        </p>

        <div class="mt-8 max-w-2xl mx-auto grid sm:grid-cols-2 gap-3 text-left">
            @foreach ($list as $f)
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-brand-100 text-brand-600 grid place-items-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-sm font-medium text-slate-600">{{ $f }}</span>
                </div>
            @endforeach
        </div>

        <a href="{{ route('dashboard') }}" class="inline-block mt-8 text-sm font-medium text-brand-600 hover:underline">← Volver al dashboard</a>
    </div>

@endsection
