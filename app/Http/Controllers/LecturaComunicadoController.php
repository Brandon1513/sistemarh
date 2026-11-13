<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use Illuminate\Http\Request;

/**
 * El dashboard y "Ver todos" avisan aquí cuando el empleado abre un comunicado.
 */
class LecturaComunicadoController extends Controller
{
    public function __invoke(Request $request, Comunicado $comunicado)
    {
        // Solo cuenta si de verdad es un comunicado que le toca ver
        $loPuedeVer = Comunicado::visibles()
            ->paraUsuario($request->user())
            ->whereKey($comunicado->id)
            ->exists();

        abort_unless($loPuedeVer, 404);

        $comunicado->marcarLeido($request->user(), 'web');

        return response()->noContent();
    }
}