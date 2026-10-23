<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PaseResource;
use App\Http\Resources\V1\SolicitudPermisoResource;
use App\Http\Resources\V1\SolicitudVacacionResource;
use App\Models\Permission;
use App\Models\SolicitudPermiso;
use App\Models\SolicitudVacacion;
use App\Services\PasesService;
use App\Services\PermisosService;
use App\Services\VacacionesService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class AprobacionController extends Controller
{
    /**
     * Solicitudes pendientes del equipo directo del jefe, las más recientes primero.
     */
    public function index(Request $request)
    {
        $jefe = $request->user();
        $deMiEquipo = fn ($q) => $q->where('supervisor_id', $jefe->id);

        $vacaciones = SolicitudVacacion::with('empleado')
        ->where('estado', 'pendiente')->whereHas('empleado', $deMiEquipo)->latest()->get();

        $permisos = SolicitudPermiso::with('empleado')
            ->where('estado', 'pendiente')->whereHas('empleado', $deMiEquipo)->latest()->get();

        $pases = Permission::with('user')
            ->where('status', 'pendiente')->whereHas('user', $deMiEquipo)->latest()->get();

        $pendientes = collect()
            ->concat(SolicitudVacacionResource::collection($vacaciones)->resolve($request))
            ->concat(SolicitudPermisoResource::collection($permisos)->resolve($request))
            ->concat(PaseResource::collection($pases)->resolve($request))
            ->sortByDesc('creada_en')
            ->values();

        return response()->json($pendientes);
    }

    public function aprobar(Request $request, string $tipo, int $id, VacacionesService $vacaciones, PermisosService $permisos, PasesService $pases)
    {
        $solicitud = $this->buscar($tipo, $id);
        Gate::authorize('approve', $solicitud);

        $resultado = match ($tipo) {
            'vacaciones' => $vacaciones->aprobar($solicitud),
            'permiso'    => $permisos->aprobar($solicitud),
            'pase'       => $pases->aprobar($solicitud, $request->user()),
        };

        return response()->json($this->recurso($tipo, $resultado)->resolve($request));
    }

    public function rechazar(Request $request, string $tipo, int $id, VacacionesService $vacaciones, PermisosService $permisos, PasesService $pases)
    {
        $solicitud = $this->buscar($tipo, $id);
        Gate::authorize('reject', $solicitud);

        $resultado = match ($tipo) {
            'vacaciones' => $vacaciones->rechazar($solicitud),
            'permiso'    => $permisos->rechazar($solicitud),
            'pase'       => $pases->rechazar($solicitud, $request->user()),
        };

        return response()->json($this->recurso($tipo, $resultado)->resolve($request));
    }

    private function buscar(string $tipo, int $id)
    {
        return match ($tipo) {
            'vacaciones' => SolicitudVacacion::findOrFail($id),
            'permiso'    => SolicitudPermiso::findOrFail($id),
            'pase'       => Permission::findOrFail($id),
        };
    }

    private function recurso(string $tipo, $solicitud): JsonResource
    {
        return match ($tipo) {
            'vacaciones' => new SolicitudVacacionResource($solicitud),
            'permiso'    => new SolicitudPermisoResource($solicitud),
            'pase'       => new PaseResource($solicitud),
        };
    }
}