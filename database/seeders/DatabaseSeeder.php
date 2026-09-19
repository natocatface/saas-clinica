<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Clinica;
use App\Models\Configuracion;
use App\Models\Especialidad;
use App\Models\Factura;
use App\Models\HistoriaClinica;
use App\Models\Medico;
use App\Models\OrdenLaboratorio;
use App\Models\Paciente;
use App\Models\Plan;
use App\Models\Producto;
use App\Models\Receta;
use App\Models\Suscripcion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Planes =====
        $planes = [];
        foreach ([
            ['nombre' => 'Basico', 'precio_mensual' => 199, 'max_usuarios' => 5, 'max_pacientes' => 500, 'descripcion' => 'Para clinicas pequenas', 'caracteristicas' => "Agenda y pacientes\nFacturacion basica\nSoporte por email", 'destacado' => false],
            ['nombre' => 'Profesional', 'precio_mensual' => 399, 'max_usuarios' => 20, 'max_pacientes' => 5000, 'descripcion' => 'El mas elegido', 'caracteristicas' => "Todo lo de Basico\nHistorias clinicas\nLaboratorio y recetas\nReportes avanzados", 'destacado' => true],
            ['nombre' => 'Premium', 'precio_mensual' => 799, 'max_usuarios' => 100, 'max_pacientes' => 50000, 'descripcion' => 'Para redes de clinicas', 'caracteristicas' => "Todo lo de Profesional\nMultiples sedes\nSoporte prioritario 24/7\nRespaldos diarios", 'destacado' => false],
        ] as $p) {
            $planes[$p['nombre']] = Plan::updateOrCreate(['nombre' => $p['nombre']], $p + ['activo' => true]);
        }

        // ===== Super Admin (sin clinica) =====
        User::updateOrCreate(['email' => 'superadmin@saas.test'], [
            'name' => 'Super Administrador',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'clinica_id' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // ===== Clinica principal =====
        $clinica = Clinica::updateOrCreate(['slug' => 'clinica-central'], [
            'nombre' => 'Clinica Central', 'nit' => '1234567', 'email' => 'contacto@central.test',
            'telefono' => '+591 2 1234567', 'ciudad' => 'La Paz', 'direccion' => 'Av. Salud #123',
            'color' => '#7c44ff', 'plan_id' => $planes['Profesional']->id, 'estado' => 'activa',
            'fecha_inicio' => now()->subMonths(4)->toDateString(),
        ]);
        $cid = $clinica->id;

        Suscripcion::firstOrCreate(
            ['clinica_id' => $cid, 'plan_id' => $planes['Profesional']->id, 'estado' => 'activa'],
            ['monto' => $planes['Profesional']->precio_mensual, 'ciclo' => 'mensual', 'inicio' => now()->subMonths(4)->toDateString(), 'fin' => now()->addMonth()->toDateString()]
        );

        // ===== Usuarios de la clinica =====
        foreach ([
            ['name' => 'Administrador General', 'email' => 'admin@clinica.test', 'role' => 'admin', 'phone' => '+591 70000000'],
            ['name' => 'Dra. Mariana Lopez', 'email' => 'medico@clinica.test', 'role' => 'medico', 'phone' => '+591 71111111'],
            ['name' => 'Recepcion Clinica', 'email' => 'recepcion@clinica.test', 'role' => 'recepcion', 'phone' => '+591 72222222'],
        ] as $u) {
            User::updateOrCreate(['email' => $u['email']], array_merge($u, [
                'clinica_id' => $cid, 'password' => Hash::make('password'),
                'is_active' => true, 'email_verified_at' => now(),
            ]));
        }

        // ===== Datos operativos de la clinica principal =====
        $esps = [
            ['nombre' => 'Medicina General', 'tarifa' => 150, 'color' => '#7c44ff'],
            ['nombre' => 'Pediatria', 'tarifa' => 180, 'color' => '#38bdf8'],
            ['nombre' => 'Cardiologia', 'tarifa' => 350, 'color' => '#34d399'],
            ['nombre' => 'Odontologia', 'tarifa' => 200, 'color' => '#fbbf24'],
            ['nombre' => 'Dermatologia', 'tarifa' => 250, 'color' => '#fb7185'],
        ];
        foreach ($esps as $e) {
            Especialidad::updateOrCreate(['clinica_id' => $cid, 'nombre' => $e['nombre']], $e + ['clinica_id' => $cid, 'activo' => true]);
        }

        $medicos = [
            ['nombres' => 'Mariana', 'apellidos' => 'Lopez', 'especialidad' => 'Cardiologia', 'matricula' => 'MED-1001', 'email' => 'mlopez@clinica.test', 'telefono' => '+591 71111111'],
            ['nombres' => 'Luis', 'apellidos' => 'Vargas', 'especialidad' => 'Pediatria', 'matricula' => 'MED-1002', 'email' => 'lvargas@clinica.test', 'telefono' => '+591 71111112'],
            ['nombres' => 'Pablo', 'apellidos' => 'Soto', 'especialidad' => 'Dermatologia', 'matricula' => 'MED-1003', 'email' => 'psoto@clinica.test', 'telefono' => '+591 71111113'],
            ['nombres' => 'Carla', 'apellidos' => 'Mendez', 'especialidad' => 'Medicina General', 'matricula' => 'MED-1004', 'email' => 'cmendez@clinica.test', 'telefono' => '+591 71111114'],
        ];
        foreach ($medicos as $m) {
            $esp = Especialidad::where('clinica_id', $cid)->where('nombre', $m['especialidad'])->first();
            Medico::updateOrCreate(['clinica_id' => $cid, 'matricula' => $m['matricula']], [
                'clinica_id' => $cid, 'nombres' => $m['nombres'], 'apellidos' => $m['apellidos'],
                'especialidad_id' => $esp?->id, 'email' => $m['email'], 'telefono' => $m['telefono'],
                'hora_inicio' => '08:00', 'hora_fin' => '16:00', 'activo' => true,
            ]);
        }

        $pacientes = [
            ['nombres' => 'Juan', 'apellidos' => 'Perez', 'ci' => '8451236', 'sexo' => 'M', 'telefono' => '+591 70011111', 'grupo_sanguineo' => 'O+', 'fecha_nacimiento' => '1988-04-12'],
            ['nombres' => 'Ana', 'apellidos' => 'Torres', 'ci' => '7745120', 'sexo' => 'F', 'telefono' => '+591 70022222', 'grupo_sanguineo' => 'A+', 'fecha_nacimiento' => '1995-09-03'],
            ['nombres' => 'Carlos', 'apellidos' => 'Ruiz', 'ci' => '9123450', 'sexo' => 'M', 'telefono' => '+591 70033333', 'grupo_sanguineo' => 'B+', 'fecha_nacimiento' => '1979-12-21'],
            ['nombres' => 'Lucia', 'apellidos' => 'Mamani', 'ci' => '6634578', 'sexo' => 'F', 'telefono' => '+591 70044444', 'grupo_sanguineo' => 'O-', 'fecha_nacimiento' => '2001-06-30'],
            ['nombres' => 'Pedro', 'apellidos' => 'Gomez', 'ci' => '5523419', 'sexo' => 'M', 'telefono' => '+591 70055555', 'grupo_sanguineo' => 'AB+', 'fecha_nacimiento' => '1990-01-15'],
        ];
        foreach ($pacientes as $p) {
            Paciente::updateOrCreate(['clinica_id' => $cid, 'ci' => $p['ci']], $p + ['clinica_id' => $cid, 'activo' => true, 'seguro' => 'Particular']);
        }

        if (Cita::where('clinica_id', $cid)->count() === 0) {
            $citas = [
                ['paciente' => 'Perez', 'medico' => 'Lopez', 'hora' => '08:30', 'estado' => 'confirmada', 'motivo' => 'Control cardiologico'],
                ['paciente' => 'Torres', 'medico' => 'Vargas', 'hora' => '09:15', 'estado' => 'en_espera', 'motivo' => 'Consulta pediatrica'],
                ['paciente' => 'Ruiz', 'medico' => 'Mendez', 'hora' => '10:00', 'estado' => 'atendida', 'motivo' => 'Chequeo general'],
                ['paciente' => 'Mamani', 'medico' => 'Soto', 'hora' => '11:30', 'estado' => 'cancelada', 'motivo' => 'Revision de piel'],
                ['paciente' => 'Gomez', 'medico' => 'Lopez', 'hora' => '12:00', 'estado' => 'programada', 'motivo' => 'Electrocardiograma'],
            ];
            foreach ($citas as $c) {
                $pac = Paciente::where('clinica_id', $cid)->where('apellidos', $c['paciente'])->first();
                $med = Medico::where('clinica_id', $cid)->where('apellidos', $c['medico'])->first();
                if ($pac && $med) {
                    Cita::create([
                        'clinica_id' => $cid, 'paciente_id' => $pac->id, 'medico_id' => $med->id,
                        'especialidad_id' => $med->especialidad_id, 'fecha' => now()->toDateString(),
                        'hora' => $c['hora'], 'motivo' => $c['motivo'], 'estado' => $c['estado'],
                    ]);
                }
            }
        }

        if (HistoriaClinica::where('clinica_id', $cid)->count() === 0) {
            $pac = Paciente::where('clinica_id', $cid)->where('apellidos', 'Perez')->first();
            $med = Medico::where('clinica_id', $cid)->where('apellidos', 'Lopez')->first();
            if ($pac && $med) {
                HistoriaClinica::create([
                    'clinica_id' => $cid, 'paciente_id' => $pac->id, 'medico_id' => $med->id, 'fecha' => now()->subDays(3)->toDateString(),
                    'motivo_consulta' => 'Dolor toracico leve', 'sintomas' => 'Molestia al esfuerzo',
                    'diagnostico' => 'Hipertension controlada', 'tratamiento' => 'Continuar medicacion',
                    'peso' => 78.5, 'talla' => 172, 'presion_arterial' => '130/85', 'temperatura' => 36.6, 'frecuencia_cardiaca' => 78,
                ]);
            }
        }

        if (Receta::where('clinica_id', $cid)->count() === 0) {
            $pac = Paciente::where('clinica_id', $cid)->where('apellidos', 'Perez')->first();
            $med = Medico::where('clinica_id', $cid)->where('apellidos', 'Lopez')->first();
            if ($pac && $med) {
                $receta = Receta::create([
                    'clinica_id' => $cid, 'paciente_id' => $pac->id, 'medico_id' => $med->id, 'fecha' => now()->toDateString(),
                    'diagnostico' => 'Hipertension arterial', 'notas' => 'Control en 30 dias. Dieta baja en sal.',
                ]);
                $receta->items()->createMany([
                    ['medicamento' => 'Enalapril 10mg', 'dosis' => '1 tableta', 'frecuencia' => 'Cada 12h', 'duracion' => '30 dias', 'indicaciones' => 'Via oral'],
                    ['medicamento' => 'Aspirina 100mg', 'dosis' => '1 tableta', 'frecuencia' => 'Cada 24h', 'duracion' => '30 dias', 'indicaciones' => 'Despues del desayuno'],
                ]);
            }
        }

        if (Factura::where('clinica_id', $cid)->count() === 0) {
            $pac = Paciente::where('clinica_id', $cid)->where('apellidos', 'Perez')->first();
            if ($pac) {
                $factura = Factura::create([
                    'clinica_id' => $cid, 'numero' => Factura::siguienteNumero(), 'paciente_id' => $pac->id, 'fecha' => now()->toDateString(),
                    'subtotal' => 350, 'descuento' => 0, 'total' => 350, 'estado' => 'pagada', 'metodo_pago' => 'efectivo',
                ]);
                $factura->items()->create(['descripcion' => 'Consulta Cardiologia', 'cantidad' => 1, 'precio_unitario' => 350, 'subtotal' => 350]);
            }
        }

        if (OrdenLaboratorio::where('clinica_id', $cid)->count() === 0) {
            $pac = Paciente::where('clinica_id', $cid)->where('apellidos', 'Torres')->first();
            $med = Medico::where('clinica_id', $cid)->where('apellidos', 'Vargas')->first();
            if ($pac) {
                $orden = OrdenLaboratorio::create([
                    'clinica_id' => $cid, 'numero' => OrdenLaboratorio::siguienteNumero(), 'paciente_id' => $pac->id, 'medico_id' => $med?->id,
                    'fecha' => now()->toDateString(), 'estado' => 'completada', 'observaciones' => 'Ayuno de 8 horas.',
                ]);
                $orden->items()->createMany([
                    ['examen' => 'Hemograma completo', 'resultado' => 'Normal', 'unidad' => '', 'valor_referencia' => ''],
                    ['examen' => 'Glucosa', 'resultado' => '92', 'unidad' => 'mg/dL', 'valor_referencia' => '70-100'],
                ]);
            }
        }

        if (Producto::where('clinica_id', $cid)->count() === 0) {
            foreach ([
                ['nombre' => 'Paracetamol 500mg', 'codigo' => 'MED-001', 'categoria' => 'Analgesico', 'unidad' => 'caja', 'stock' => 120, 'stock_minimo' => 20, 'precio_compra' => 8, 'precio_venta' => 15],
                ['nombre' => 'Amoxicilina 500mg', 'codigo' => 'MED-002', 'categoria' => 'Antibiotico', 'unidad' => 'caja', 'stock' => 15, 'stock_minimo' => 20, 'precio_compra' => 20, 'precio_venta' => 35],
                ['nombre' => 'Guantes de latex', 'codigo' => 'INS-001', 'categoria' => 'Insumo', 'unidad' => 'caja', 'stock' => 60, 'stock_minimo' => 10, 'precio_compra' => 25, 'precio_venta' => 40],
                ['nombre' => 'Jeringa 5ml', 'codigo' => 'INS-002', 'categoria' => 'Insumo', 'unidad' => 'unidad', 'stock' => 400, 'stock_minimo' => 50, 'precio_compra' => 1.2, 'precio_venta' => 2.5],
            ] as $p) {
                Producto::create($p + ['clinica_id' => $cid, 'activo' => true, 'vencimiento' => now()->addYear()->toDateString()]);
            }
        }

        foreach ([
            'clinica_nombre' => 'Clinica Central', 'clinica_nit' => '1234567', 'clinica_ciudad' => 'La Paz',
            'clinica_direccion' => 'Av. Salud #123', 'clinica_telefono' => '+591 2 1234567',
            'clinica_email' => 'contacto@central.test', 'moneda' => 'Bs', 'mensaje_pie' => 'Gracias por su preferencia.',
        ] as $k => $v) {
            Configuracion::firstOrCreate(['clinica_id' => $cid, 'clave' => $k], ['valor' => $v]);
        }

        // ===== Otras clinicas demo (para el panel super admin) =====
        $demo = [
            ['nombre' => 'Clinica San Rafael', 'ciudad' => 'Cochabamba', 'color' => '#0891b2', 'plan' => 'Basico', 'estado' => 'activa', 'admin' => 'admin@sanrafael.test'],
            ['nombre' => 'Centro Medico Vida', 'ciudad' => 'Santa Cruz', 'color' => '#16a34a', 'plan' => 'Premium', 'estado' => 'prueba', 'admin' => 'admin@vida.test'],
            ['nombre' => 'Policlinico Norte', 'ciudad' => 'El Alto', 'color' => '#db2777', 'plan' => 'Basico', 'estado' => 'suspendida', 'admin' => 'admin@norte.test'],
        ];
        foreach ($demo as $d) {
            $plan = $planes[$d['plan']];
            $cl = Clinica::updateOrCreate(['slug' => Str::slug($d['nombre'])], [
                'nombre' => $d['nombre'], 'ciudad' => $d['ciudad'], 'color' => $d['color'],
                'plan_id' => $plan->id, 'estado' => $d['estado'], 'email' => $d['admin'],
                'fecha_inicio' => now()->subMonths(rand(1, 3))->toDateString(),
            ]);
            User::updateOrCreate(['email' => $d['admin']], [
                'name' => 'Admin '.$d['nombre'], 'clinica_id' => $cl->id, 'password' => Hash::make('password'),
                'role' => 'admin', 'is_active' => true, 'email_verified_at' => now(),
            ]);
            Suscripcion::firstOrCreate(
                ['clinica_id' => $cl->id, 'plan_id' => $plan->id],
                ['estado' => $d['estado'] === 'suspendida' ? 'vencida' : 'activa', 'monto' => $plan->precio_mensual, 'ciclo' => 'mensual', 'inicio' => now()->subMonths(2)->toDateString(), 'fin' => now()->addMonth()->toDateString()]
            );
        }
    }
}
