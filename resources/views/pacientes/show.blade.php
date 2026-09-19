@extends('layouts.app')
@section('title', $paciente->nombre_completo)

@php
    $estadoColor = [
        'programada' => 'bg-sky-100 text-sky-700',
        'confirmada' => 'bg-emerald-100 text-emerald-700',
        'en_espera'  => 'bg-amber-100 text-amber-700',
        'atendida'   => 'bg-brand-100 text-brand-700',
        'cancelada'  => 'bg-rose-100 text-rose-700',
    ];
@endphp

@section('content')

    <nav class="text-sm text-slate-400 mb-5">
        <a href="{{ route('pacientes.index') }}" class="hover:text-brand-600">Pacientes</a>
        <span class="mx-1.5">/</span><span class="text-slate-600 font-medium">{{ $paciente->nombre_completo }}</span>
    </nav>

    <div class="grid lg:grid-cols-3 gap-6">

        <!-- Ficha -->
        <div class="rounded-3xl bg-white shadow-card p-6">
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-brand-500 to-brand-700 text-white grid place-items-center text-2xl font-bold mb-3">
                    {{ strtoupper(substr($paciente->nombres,0,1).substr($paciente->apellidos,0,1)) }}
                </div>
                <h2 class="text-lg font-bold text-slate-800">{{ $paciente->nombre_completo }}</h2>
                <p class="text-sm text-slate-400">{{ $paciente->edad !== null ? $paciente->edad.' años' : 'Edad no registrada' }}</p>
                <span class="mt-2 inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $paciente->activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $paciente->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </div>

            <dl class="mt-6 space-y-3 text-sm">
                @foreach ([
                    'CI / Documento' => $paciente->ci,
                    'Sexo' => ['M'=>'Masculino','F'=>'Femenino','O'=>'Otro'][$paciente->sexo] ?? null,
                    'Telefono' => $paciente->telefono,
                    'Email' => $paciente->email,
                    'Direccion' => $paciente->direccion,
                    'Grupo sanguineo' => $paciente->grupo_sanguineo,
                    'Seguro' => $paciente->seguro,
                    'Alergias' => $paciente->alergias,
                ] as $label => $value)
                    <div class="flex justify-between gap-4 border-b border-slate-50 pb-2">
                        <dt class="text-slate-400">{{ $label }}</dt>
                        <dd class="font-medium text-slate-700 text-right">{{ $value ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>

            <a href="{{ route('pacientes.edit', $paciente) }}" class="mt-6 block text-center px-4 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-semibold hover:bg-brand-700 transition">Editar paciente</a>
        </div>

        <!-- Historial de citas -->
        <div class="lg:col-span-2 rounded-3xl bg-white shadow-card overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="font-bold text-slate-800">Historial de citas</h2>
                <span class="text-xs text-slate-400">{{ $paciente->citas->count() }} registro(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wide text-slate-400 bg-slate-50">
                            <th class="px-6 py-3 font-semibold">Fecha</th>
                            <th class="px-6 py-3 font-semibold">Medico</th>
                            <th class="px-6 py-3 font-semibold">Especialidad</th>
                            <th class="px-6 py-3 font-semibold">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($paciente->citas->sortByDesc('fecha') as $cita)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 font-medium text-slate-700">{{ $cita->fecha->format('d/m/Y') }} · {{ substr($cita->hora,0,5) }}</td>
                                <td class="px-6 py-3.5 text-slate-500">Dr(a). {{ $cita->medico?->nombre_completo ?? '—' }}</td>
                                <td class="px-6 py-3.5 text-slate-500">{{ $cita->especialidad?->nombre ?? '—' }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-block text-xs font-semibold px-2.5 py-1 rounded-full {{ $estadoColor[$cita->estado] ?? 'bg-slate-100 text-slate-600' }}">{{ $cita->estadoLabel() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">Este paciente aun no tiene citas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
