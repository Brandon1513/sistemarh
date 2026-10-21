<?php

namespace App\Services\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

trait GestionaSolicitudes
{
    /**
     * Vuelve a leer la solicitud bloqueándola, para que dos personas no la procesen a la vez.
     * La columna de estado cambia según la tabla: 'estado' en vacaciones y permisos, 'status' en pases.
     */
    protected function bloquearPendiente(Model $solicitud, string $columnaEstado = 'estado'): Model
    {
        $actual = $solicitud->newQuery()->whereKey($solicitud->getKey())->lockForUpdate()->firstOrFail();

        if (strtolower((string) $actual->{$columnaEstado}) !== 'pendiente') {
            $this->error($columnaEstado, 'Esta solicitud ya fue procesada.');
        }

        return $actual;
    }

    /**
     * Ejecuta una notificación sin que su fallo revierta o interrumpa la acción principal.
     */
    protected function sinRomper(callable $accion): void
    {
        try {
            $accion();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function error(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages([$campo => $mensaje]);
    }
}