<?php

namespace App\Jobs;

use App\Http\Controllers\FormularioController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSolicitudEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $solicitudId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $solicitudId)
    {
        $this->solicitudId = $solicitudId;
        // Prioridad por defecto y cola pueden ajustarse vía configuración si es necesario
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            app(FormularioController::class)->enviarPorSolicitud($this->solicitudId);
        } catch (\Throwable $e) {
            Log::error('[SendSolicitudEmailsJob] Error', ['id' => $this->solicitudId, 'error' => $e->getMessage()]);
        }
    }
}