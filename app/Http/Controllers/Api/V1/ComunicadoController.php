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
            'tipo'       => ['nullable', Rule::in([...array_keys(Comunicado::TIPOS), 'celebraciones'])],
            'por_pagina' => 'nullable|integer|min:1|max:30',
        ]);

        $tipo = $request->input('tipo');

        $consulta = $tipo === 'celebraciones'
            // Las celebraciones de los últimos 3 meses, aunque ya estén archivadas
            ? Comunicado::whereIn('tipo', ['cumpleanos', 'aniversario'])
            ->whereIn('estado', ['publicado', 'archivado'])
            ->where('publicado_en', '>=', now()->subMonths(3))
            ->orderByDesc('publicado_en')
            : Comunicado::visibles()->when($tipo, fn($q) => $q->where('tipo', $tipo));

        $pagina = $consulta
            ->paraUsuario($request->user())
            ->conLeidoPor($request->user())
            ->withCount('imagenes')
            ->paginate($request->integer('por_pagina', 15));
        return response()->json([
            'items'   => ComunicadoResource::collection($pagina->items())->resolve($request),
            'pagina'  => $pagina->currentPage(),
            'hay_mas' => $pagina->hasMorePages(),
        ]);
    }

    /**
     * Un comunicado con su galería. Los publicados, y las celebraciones archivadas de los últimos 3 meses.
     */
    public function show(Request $request, Comunicado $comunicado)
    {
        $usuario = $request->user();

        $esVisible = Comunicado::visibles()->paraUsuario($usuario)->whereKey($comunicado->id)->exists();

        $esCelebracionReciente = in_array($comunicado->tipo, ['cumpleanos', 'aniversario'], true)
            && in_array($comunicado->estado, ['publicado', 'archivado'], true)
            && $comunicado->publicado_en?->gte(now()->subMonths(3))
            && Comunicado::paraUsuario($usuario)->whereKey($comunicado->id)->exists();

        abort_unless($esVisible || $esCelebracionReciente, 404);

        $comunicado->marcarLeido($usuario, 'app');

        return response()->json(
            (new ComunicadoResource($comunicado->load('imagenes')))->resolve($request)
        );
    }
}
