<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ComunicadoResource;
use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComunicadoController extends Controller
{
    /**
     * Comunicados visibles, por páginas. Filtro opcional por tipo.
     */
    public function index(Request $request)
    {
        $request->validate([
            'tipo'       => ['nullable', Rule::in(array_keys(Comunicado::TIPOS))],
            'por_pagina' => 'nullable|integer|min:1|max:30',
        ]);

        $pagina = Comunicado::visibles()
        ->paraUsuario($request->user())
        ->withCount('imagenes')
        ->when($request->filled('tipo'), fn ($q) => $q->where('tipo', $request->input('tipo')))
        ->paginate($request->integer('por_pagina', 15));

        return response()->json([
            'items'   => ComunicadoResource::collection($pagina->items())->resolve($request),
            'pagina'  => $pagina->currentPage(),
            'hay_mas' => $pagina->hasMorePages(),
        ]);
    }

    /**
     * Un comunicado con su galería. Solo si está visible para los empleados.
     */
    public function show(Request $request, Comunicado $comunicado)
    {
        $loPuedeVer = Comunicado::visibles()
            ->paraUsuario($request->user())
            ->whereKey($comunicado->id)
            ->exists();

        abort_unless($loPuedeVer, 404);

        return response()->json(
            (new ComunicadoResource($comunicado->load('imagenes')))->resolve($request)
        );
    }
}