<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Clinica;
use App\Models\Configuracion;
use App\Models\Especialidad;
use App\Models\Factura;
use App\Models\HistoriaClinica;
use App\Models\Medico;
use App\Models\MovimientoInventario;
use App\Models\OrdenLaboratorio;
use App\Models\Paciente;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Datos de demostracion: ~10 registros por modulo, repartidos en distintas
 * fechas de los ultimos 8 meses, para que el dashboard (citas por mes,
 * ingresos por mes, especialidades y estados) muestre informacion real.
 *
 * Ejecutar:  php artisan db:seed --class=DemoDataSeeder
 * Es idempotente: si ya se corrio, no vuelve a duplicar.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $clinica = Clinica::where('slug', 'clinica-central')->first()
            ?? Clinica::orderBy('id')->first();

        if (! $clinica) {
            $this->command->error('No existe ninguna clinica. Corre primero "php artisan db:seed".');
            return;
        }
        $cid = $clinica->id;

        // Evitar duplicados si se vuelve a ejecutar
        // if (Configuracion::where('clinica_id', $cid)->where('clave', 'demo_data_seeded')->exists()) {
        //     $this->command->warn('DemoDataSeeder ya fue ejecutado anteriormente. Nada que hacer.');
        //     return;
        // }

        $hoy = Carbon::today();

        // ===================== ESPECIALIDADES (completar a 10) =====================
        $espExtra = [
            ['nombre' => 'Ginecologia', 'tarifa' => 220, 'color' => '#a855f7'],
            ['nombre' => 'Traumatologia', 'tarifa' => 280, 'color' => '#f97316'],
            ['nombre' => 'Oftalmologia', 'tarifa' => 240, 'color' => '#0ea5e9'],
            ['nombre' => 'Neurologia', 'tarifa' => 360, 'color' => '#14b8a6'],
            ['nombre' => 'Otorrinolaringologia', 'tarifa' => 230, 'color' => '#ef4444'],
        ];
        foreach ($espExtra as $e) {
            Especialidad::updateOrCreate(
                ['clinica_id' => $cid, 'nombre' => $e['nombre']],
                $e + ['clinica_id' => $cid, 'activo' => true]
            );
        }
        $especialidades = Especialidad::where('clinica_id', $cid)->get();

        // ===================== MEDICOS (agregar 6 -> total ~10) =====================
        $medicosNuevos = [
            ['nombres' => 'Sofia',  'apellidos' => 'Quispe',   'esp' => 'Ginecologia',          'mat' => 'MED-2001'],
            ['nombres' => 'Andres', 'apellidos' => 'Rojas',    'esp' => 'Traumatologia',        'mat' => 'MED-2002'],
            ['nombres' => 'Valeria','apellidos' => 'Flores',   'esp' => 'Oftalmologia',         'mat' => 'MED-2003'],
            ['nombres' => 'Diego',  'apellidos' => 'Castro',   'esp' => 'Neurologia',           'mat' => 'MED-2004'],
            ['nombres' => 'Camila', 'apellidos' => 'Herrera',  'esp' => 'Otorrinolaringologia', 'mat' => 'MED-2005'],
            ['nombres' => 'Jorge',  'apellidos' => 'Salinas',  'esp' => 'Medicina General',     'mat' => 'MED-2006'],
        ];
        foreach ($medicosNuevos as $m) {
            $esp = $especialidades->firstWhere('nombre', $m['esp']);
            Medico::updateOrCreate(
                ['clinica_id' => $cid, 'matricula' => $m['mat']],
                [
                    'clinica_id' => $cid, 'nombres' => $m['nombres'], 'apellidos' => $m['apellidos'],
                    'especialidad_id' => $esp?->id,
                    'email' => strtolower($m['apellidos']).'@clinica.test',
                    'telefono' => '+591 7'.rand(1000000, 9999999),
                    'hora_inicio' => '08:00', 'hora_fin' => '17:00', 'activo' => true,
                ]
            );
        }
        $medicos = Medico::where('clinica_id', $cid)->get();

        // ===================== PACIENTES (10 nuevos) =====================
        $nombres = ['Mateo', 'Valentina', 'Sebastian', 'Isabella', 'Nicolas', 'Camila', 'Daniel', 'Antonella', 'Gabriel', 'Renata'];
        $apellidos = ['Choque', 'Aguilar', 'Cespedes', 'Nina', 'Velasco', 'Ferrufino', 'Paredes', 'Montano', 'Sandoval', 'Ticona'];
        $sangre = ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-'];
        $segs = ['Particular', 'Seguro Privado', 'Caja Nacional'];
        $pacientesNuevos = [];
        for ($i = 0; $i < 10; $i++) {
            $pacientesNuevos[] = Paciente::updateOrCreate(
                ['clinica_id' => $cid, 'ci' => '90'.str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT)],
                [
                    'clinica_id' => $cid, 'nombres' => $nombres[$i], 'apellidos' => $apellidos[$i],
                    'sexo' => $i % 2 === 0 ? 'M' : 'F',
                    'telefono' => '+591 6'.rand(1000000, 9999999),
                    'email' => strtolower($nombres[$i].'.'.$apellidos[$i]).'@mail.test',
                    'grupo_sanguineo' => $sangre[$i % count($sangre)],
                    'fecha_nacimiento' => Carbon::create(rand(1970, 2010), rand(1, 12), rand(1, 28))->toDateString(),
                    'seguro' => $segs[$i % count($segs)],
                    'direccion' => 'Zona '.($i + 1).', calle '.rand(1, 50),
                    'activo' => true,
                ]
            );
        }
        $pacientes = Paciente::where('clinica_id', $cid)->get();

        // ===================== USUARIOS Y ROLES (5 nuevos) =====================
        $usuarios = [
            ['name' => 'Dra. Sofia Quispe', 'email' => 'squispe@clinica.test', 'role' => 'medico'],
            ['name' => 'Dr. Andres Rojas', 'email' => 'arojas@clinica.test', 'role' => 'medico'],
            ['name' => 'Recepcion Tarde', 'email' => 'recepcion2@clinica.test', 'role' => 'recepcion'],
            ['name' => 'Caja Facturacion', 'email' => 'caja@clinica.test', 'role' => 'recepcion'],
            ['name' => 'Asistente Admin', 'email' => 'asistente@clinica.test', 'role' => 'admin'],
        ];
        foreach ($usuarios as $u) {
            User::updateOrCreate(['email' => $u['email']], array_merge($u, [
                'clinica_id' => $cid, 'password' => Hash::make('password'),
                'phone' => '+591 7'.rand(1000000, 9999999),
                'is_active' => true, 'email_verified_at' => now(),
            ]));
        }

        // ===================== CITAS (12, repartidas en 8 meses) =====================
        $estados = ['programada', 'confirmada', 'en_espera', 'atendida', 'atendida', 'cancelada'];
        $horas = ['08:30', '09:15', '10:00', '11:30', '12:00', '14:30', '15:45', '16:30'];
        $motivos = ['Control general', 'Consulta de rutina', 'Chequeo anual', 'Seguimiento', 'Dolor abdominal', 'Revision de examenes', 'Control postoperatorio', 'Consulta especializada'];
        for ($i = 0; $i < 12; $i++) {
            $med = $medicos[$i % $medicos->count()];
            $pac = $pacientes[$i % $pacientes->count()];
            // repartir en los ultimos 8 meses (1-2 por mes) + algunas hoy
            $fecha = $i < 8
                ? $hoy->copy()->subMonths(7 - $i)->day(rand(3, 26))
                : $hoy->copy()->day(min($hoy->day, 28)); // las ultimas, este mes
            Cita::create([
                'clinica_id' => $cid, 'paciente_id' => $pac->id, 'medico_id' => $med->id,
                'especialidad_id' => $med->especialidad_id,
                'fecha' => $fecha->toDateString(),
                'hora' => $horas[$i % count($horas)],
                'motivo' => $motivos[$i % count($motivos)],
                'estado' => $estados[$i % count($estados)],
            ]);
        }

        // ===================== HISTORIAS CLINICAS (10, en distintas fechas) =====================
        $diags = ['Hipertension controlada', 'Faringitis aguda', 'Gastritis', 'Migrana', 'Dermatitis', 'Lumbalgia', 'Anemia leve', 'Bronquitis', 'Alergia estacional', 'Control sano'];
        for ($i = 0; $i < 10; $i++) {
            $med = $medicos[$i % $medicos->count()];
            $pac = $pacientes[$i % $pacientes->count()];
            HistoriaClinica::create([
                'clinica_id' => $cid, 'paciente_id' => $pac->id, 'medico_id' => $med->id,
                'fecha' => $hoy->copy()->subMonths(7 - ($i % 8))->day(rand(2, 27))->toDateString(),
                'motivo_consulta' => $motivos[$i % count($motivos)],
                'sintomas' => 'Sintomas referidos por el paciente',
                'diagnostico' => $diags[$i],
                'tratamiento' => 'Tratamiento indicado segun diagnostico',
                'peso' => rand(50, 95) + 0.5, 'talla' => rand(150, 185),
                'presion_arterial' => rand(100, 140).'/'.rand(60, 90),
                'temperatura' => 36 + (rand(0, 15) / 10),
                'frecuencia_cardiaca' => rand(60, 95),
            ]);
        }

        // ===================== RECETAS (10, con items) =====================
        $meds = [
            ['Paracetamol 500mg', '1 tableta', 'Cada 8h', '5 dias'],
            ['Ibuprofeno 400mg', '1 tableta', 'Cada 12h', '7 dias'],
            ['Amoxicilina 500mg', '1 capsula', 'Cada 8h', '7 dias'],
            ['Omeprazol 20mg', '1 capsula', 'En ayunas', '14 dias'],
            ['Loratadina 10mg', '1 tableta', 'Cada 24h', '10 dias'],
        ];
        for ($i = 0; $i < 10; $i++) {
            $med = $medicos[$i % $medicos->count()];
            $pac = $pacientes[$i % $pacientes->count()];
            $receta = Receta::create([
                'clinica_id' => $cid, 'paciente_id' => $pac->id, 'medico_id' => $med->id,
                'fecha' => $hoy->copy()->subMonths(7 - ($i % 8))->day(rand(2, 27))->toDateString(),
                'diagnostico' => $diags[$i],
                'notas' => 'Reposo e hidratacion. Control si persisten sintomas.',
            ]);
            $m1 = $meds[$i % count($meds)];
            $m2 = $meds[($i + 1) % count($meds)];
            $receta->items()->createMany([
                ['medicamento' => $m1[0], 'dosis' => $m1[1], 'frecuencia' => $m1[2], 'duracion' => $m1[3], 'indicaciones' => 'Via oral'],
                ['medicamento' => $m2[0], 'dosis' => $m2[1], 'frecuencia' => $m2[2], 'duracion' => $m2[3], 'indicaciones' => 'Despues de comidas'],
            ]);
        }

        // ===================== FACTURAS (12 pagadas, repartidas en 6 meses) =====================
        $metodos = ['efectivo', 'tarjeta', 'transferencia', 'seguro'];
        for ($i = 0; $i < 12; $i++) {
            $pac = $pacientes[$i % $pacientes->count()];
            $esp = $especialidades[$i % $especialidades->count()];
            $tarifa = (float) ($esp->tarifa ?: rand(150, 350));
            $cantidad = rand(1, 2);
            $subtotal = $tarifa * $cantidad;
            $descuento = $i % 4 === 0 ? round($subtotal * 0.10, 2) : 0;
            $total = $subtotal - $descuento;
            // 10 repartidas en los ultimos 6 meses, 2 este mes
            $fecha = $i < 10
                ? $hoy->copy()->subMonths(5 - intdiv($i, 2))->day(rand(2, 26))
                : $hoy->copy()->day(min($hoy->day, 28));
            $factura = Factura::create([
                'clinica_id' => $cid, 'numero' => Factura::siguienteNumero(),
                'paciente_id' => $pac->id, 'fecha' => $fecha->toDateString(),
                'subtotal' => $subtotal, 'descuento' => $descuento, 'total' => $total,
                'estado' => $i === 11 ? 'pendiente' : 'pagada', // 1 pendiente para variedad
                'metodo_pago' => $metodos[$i % count($metodos)],
                'notas' => 'Atencion '.$esp->nombre,
            ]);
            $factura->items()->create([
                'descripcion' => 'Consulta '.$esp->nombre,
                'cantidad' => $cantidad, 'precio_unitario' => $tarifa, 'subtotal' => $subtotal,
            ]);
        }

        // ===================== ORDENES DE LABORATORIO (10, con items) =====================
        $examenes = [
            ['Hemograma completo', 'Normal', '', ''],
            ['Glucosa en ayunas', (string) rand(80, 110), 'mg/dL', '70-100'],
            ['Colesterol total', (string) rand(150, 230), 'mg/dL', '<200'],
            ['Trigliceridos', (string) rand(90, 200), 'mg/dL', '<150'],
            ['Creatinina', '0.'.rand(7, 12), 'mg/dL', '0.6-1.2'],
        ];
        $estadosLab = ['solicitada', 'en_proceso', 'completada', 'completada', 'entregada'];
        for ($i = 0; $i < 10; $i++) {
            $pac = $pacientes[$i % $pacientes->count()];
            $med = $medicos[$i % $medicos->count()];
            $orden = OrdenLaboratorio::create([
                'clinica_id' => $cid, 'numero' => OrdenLaboratorio::siguienteNumero(),
                'paciente_id' => $pac->id, 'medico_id' => $med->id,
                'fecha' => $hoy->copy()->subMonths(7 - ($i % 8))->day(rand(2, 27))->toDateString(),
                'estado' => $estadosLab[$i % count($estadosLab)],
                'observaciones' => 'Ayuno de 8 horas.',
            ]);
            $e1 = $examenes[$i % count($examenes)];
            $e2 = $examenes[($i + 2) % count($examenes)];
            $orden->items()->createMany([
                ['examen' => $e1[0], 'resultado' => $e1[1], 'unidad' => $e1[2], 'valor_referencia' => $e1[3]],
                ['examen' => $e2[0], 'resultado' => $e2[1], 'unidad' => $e2[2], 'valor_referencia' => $e2[3]],
            ]);
        }

        // ===================== PRODUCTOS / FARMACIA (10) =====================
        $productos = [
            ['Diclofenaco 50mg', 'Analgesico', 'caja', 80, 20, 6, 12],
            ['Loratadina 10mg', 'Antialergico', 'caja', 45, 15, 7, 14],
            ['Omeprazol 20mg', 'Gastrico', 'caja', 30, 20, 9, 18],
            ['Suero fisiologico 500ml', 'Insumo', 'frasco', 120, 30, 4, 8],
            ['Alcohol en gel 250ml', 'Insumo', 'frasco', 18, 25, 10, 20],
            ['Vendas elasticas', 'Insumo', 'unidad', 200, 40, 3, 6],
            ['Mascarillas N95', 'Insumo', 'caja', 12, 20, 30, 55],
            ['Termometro digital', 'Equipo', 'unidad', 25, 10, 35, 60],
            ['Gasas esteriles', 'Insumo', 'caja', 90, 20, 5, 10],
            ['Ibuprofeno 400mg', 'Analgesico', 'caja', 60, 20, 8, 16],
        ];
        foreach ($productos as $i => $p) {
            $prod = Producto::updateOrCreate(
                ['clinica_id' => $cid, 'codigo' => 'PRD-'.str_pad((string) ($i + 101), 3, '0', STR_PAD_LEFT)],
                [
                    'clinica_id' => $cid, 'nombre' => $p[0], 'categoria' => $p[1], 'unidad' => $p[2],
                    'stock' => $p[3], 'stock_minimo' => $p[4], 'precio_compra' => $p[5], 'precio_venta' => $p[6],
                    'vencimiento' => $hoy->copy()->addMonths(rand(6, 24))->toDateString(), 'activo' => true,
                ]
            );
            // un movimiento de inventario de entrada por cada producto
            MovimientoInventario::create([
                'producto_id' => $prod->id, 'tipo' => 'entrada', 'cantidad' => $p[3],
                'motivo' => 'Compra inicial de inventario',
                'fecha' => $hoy->copy()->subMonths($i % 6)->day(rand(2, 26)),
            ]);
        }

        // marcar como ejecutado
        Configuracion::firstOrCreate(
            ['clinica_id' => $cid, 'clave' => 'demo_data_seeded'],
            ['valor' => now()->toDateTimeString()]
        );

        $this->command->info('DemoDataSeeder: datos de demostracion creados correctamente.');
    }
}
