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
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    private const LIMITE = 50;

    /**
     * Todas las solicitudes del empleado (vacaciones, permisos y pases) en una sola lista,
     * de la más reciente a la más antigua. Filtro opcional: ?estado=pendiente|aprobado|rechazado
     */
    public function index(Request $request)
    {
        $request->validate(['estado' => 'nullable|in:pendiente,aprobado,rechazado']);

        $user = $request->user();
        $estado = $request->query('estado');

        $vacaciones = SolicitudVacacion::where('empleado_id', $user->id)
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->latest()->limit(self::LIMITE)->get();

        $permisos = SolicitudPermiso::where('empleado_id', $user->id)
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->latest()->limit(self::LIMITE)->get();

        $pases = Permission::where('user_id', $user->id)
            ->when($estado, fn ($q) => $q->where('status', $estado))
            ->latest()->limit(self::LIMITE)->get();

        $solicitudes = collect()
            ->concat(SolicitudVacacionResource::collection($vacaciones)->resolve($request))
            ->concat(SolicitudPermisoResource::collection($permisos)->resolve($request))
            ->concat(PaseResource::collection($pases)->resolve($request))
            ->sortByDesc('creada_en')
            ->take(self::LIMITE)
            ->values();

        return response()->json($solicitudes);
    }

    /**
     * Opciones para los formularios de la app, tomadas de los servicios.
     */
    public function catalogos()
    {
        return response()->json([
            'permisos' => [
                'tipos' => PermisosService::TIPOS,
                'goce'  => PermisosService::GOCE,
            ],
            'pases' => [
                'tipos' => PasesService::TIPOS,
            ],
        ]);
    }
}