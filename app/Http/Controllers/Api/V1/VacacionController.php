<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SolicitudVacacionResource;
use App\Models\PeriodoVacacion;
use App\Models\SolicitudVacacion;
use App\Services\VacacionesService;
use App\Support\CalendarioLaboral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VacacionController extends Controller
{
    /**
     * Periodos activos del empleado con su saldo.
     */
    public function periodos(Request $request)
    {
        return PeriodoVacacion::where('empleado_id', $request->user()->id)
            ->where('activo', 1)
            ->orderBy('anio')
            ->get(['anio', 'dias_corresponden', 'dias_disponibles']);
    }

    /**
     * Días festivos del año actual y el siguiente, con su nombre.
     */
    public function festivos()
    {
        return collect(CalendarioLaboral::festivosEntre(now()->year, now()->year + 1))
            ->map(fn ($nombre, $fecha) => ['fecha' => $fecha, 'nombre' => $nombre])
            ->values();
    }

    public function store(Request $request, VacacionesService $vacaciones)
    {
        $datos = $request->validate([
            'periodo_correspondiente' => 'required|integer',
            'fecha_inicio'            => 'required|date',
            'fecha_fin'               => 'required|date',
        ]);

        $solicitud = $vacaciones->solicitar(
            $request->user(),
            (int) $datos['periodo_correspondiente'],
            $datos['fecha_inicio'],
            $datos['fecha_fin'],
        );

        return (new SolicitudVacacionResource($solicitud))->response()->setStatusCode(201);
    }

    public function destroy(SolicitudVacacion $solicitud, VacacionesService $vacaciones)
    {
        Gate::authorize('delete', $solicitud);

        $vacaciones->cancelar($solicitud);

        return response()->noContent();
    }
}