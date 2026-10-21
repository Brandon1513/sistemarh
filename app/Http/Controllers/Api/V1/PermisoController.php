<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SolicitudPermisoResource;
use App\Models\SolicitudPermiso;
use App\Services\PermisosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermisoController extends Controller
{
    public function store(Request $request, PermisosService $permisos)
    {
        $datos = $request->validate(PermisosService::reglas());

        $permiso = $permisos->solicitar($request->user(), $datos, $request->file('archivo'));

        return (new SolicitudPermisoResource($permiso))->response()->setStatusCode(201);
    }

    public function destroy(SolicitudPermiso $permiso, PermisosService $permisos)
    {
        Gate::authorize('delete', $permiso);

        $permisos->cancelar($permiso);

        return response()->noContent();
    }
}