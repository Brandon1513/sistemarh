<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PaseResource;
use App\Models\Permission;
use App\Services\PasesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaseController extends Controller
{
    public function store(Request $request, PasesService $pases)
    {
        $datos = $request->validate(PasesService::reglas());

        $pase = $pases->solicitar($request->user(), $datos, $request->file('supporting_document'));

        return (new PaseResource($pase))->response()->setStatusCode(201);
    }

    public function destroy(Permission $pase, PasesService $pases)
    {
        Gate::authorize('delete', $pase);

        $pases->cancelar($pase);

        return response()->noContent();
    }
}