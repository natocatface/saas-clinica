<?php

namespace App\Console\Commands;

use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatoriosCitas extends Command
{
    protected $signature = 'citas:recordatorios {--dias=1 : Dias de anticipacion}';

    protected $description = 'Envia recordatorios de las citas proximas y las marca como recordadas';

    public function handle(): int
    {
        $fecha = Carbon::today()->addDays((int) $this->option('dias'))->toDateString();

        // Sin contexto de usuario, el scope por clinica esta inactivo: procesa todas las clinicas.
        $citas = Cita::with(['paciente', 'medico'])
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->where('recordatorio_enviado', false)
            ->get();

        $enviados = 0;

        foreach ($citas as $cita) {
            $email = $cita->paciente?->email;

            if ($email) {
                $cuerpo = "Estimado(a) {$cita->paciente->nombre_completo},\n\n"
                    ."Le recordamos su cita el {$cita->fecha->format('d/m/Y')} a las ".substr($cita->hora, 0, 5)
                    ." con Dr(a). ".($cita->medico?->nombre_completo ?? '')."\n\nGracias.";
                try {
                    Mail::raw($cuerpo, function ($m) use ($email) {
                        $m->to($email)->subject('Recordatorio de su cita');
                    });
                } catch (\Throwable $e) {
                    $this->warn("No se pudo enviar a {$email}: {$e->getMessage()}");
                }
            }

            $cita->update(['recordatorio_enviado' => true, 'recordatorio_at' => now()]);
            $enviados++;
        }

        $this->info("Recordatorios procesados para {$fecha}: {$enviados}");

        return self::SUCCESS;
    }
}
