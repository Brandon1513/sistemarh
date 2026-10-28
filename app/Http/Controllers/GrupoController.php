<?php

namespace App\Http\Controllers;

use App\Models\ComunicadoDestino;
use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Grupos de la intranet para dirigir comunicados ("Comité de seguridad", "Jefes de planta"...).
 * Los administra RH.
 */
class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::withCount('usuarios')->with('creador')->orderBy('nombre')->get();

        // En cuántos comunicados se usa cada grupo
        $usos = ComunicadoDestino::where('tipo', 'grupo')
            ->selectRaw('destino_id, count(*) as total')
            ->groupBy('destino_id')
            ->pluck('total', 'destino_id');

        return view('grupos.index', compact('grupos', 'usos'));
    }

    public function create()
    {
        return view('grupos.form', ['grupo' => new Grupo(), 'integrantes' => collect()]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);

        $grupo = DB::transaction(function () use ($datos, $request) {
            $grupo = Grupo::create([
                'nombre'      => $datos['nombre'],
                'descripcion' => $datos['descripcion'] ?? null,
                'creado_por'  => $request->user()->id,
            ]);
            $grupo->usuarios()->sync($datos['integrantes'] ?? []);

            return $grupo;
        });

        return redirect()->route('grupos.index')->with('success', "Grupo «{$grupo->nombre}» creado.");
    }

    public function edit(Grupo $grupo)
    {
        return view('grupos.form', [
            'grupo'       => $grupo,
            'integrantes' => $this->integrantesParaVista($grupo->usuarios()->with('sede')->orderBy('name')->get()),
        ]);
    }

    public function update(Request $request, Grupo $grupo)
    {
        $datos = $this->validar($request, $grupo);

        DB::transaction(function () use ($grupo, $datos) {
            $grupo->update([
                'nombre'      => $datos['nombre'],
                'descripcion' => $datos['descripcion'] ?? null,
            ]);
            $grupo->usuarios()->sync($datos['integrantes'] ?? []);
        });

        return redirect()->route('grupos.index')->with('success', "Grupo «{$grupo->nombre}» actualizado.");
    }

    public function destroy(Grupo $grupo)
    {
        // Si se borrara un grupo en uso, sus comunicados se quedarían sin destinatarios
        // y pasarían a ser para todos
        $usos = ComunicadoDestino::where('tipo', 'grupo')->where('destino_id', $grupo->id)->count();

        if ($usos > 0) {
            return back()->with('warning', "«{$grupo->nombre}» se usa en {$usos} " . ($usos === 1 ? 'comunicado' : 'comunicados')
                . '. Quítalo de esos comunicados antes de borrarlo.');
        }

        $grupo->delete();

        return redirect()->route('grupos.index')->with('success', "Grupo «{$grupo->nombre}» eliminado.");
    }

    private function validar(Request $request, ?Grupo $grupo = null): array
    {
        return $request->validate([
            'nombre'        => ['required', 'string', 'max:100', Rule::unique('grupos', 'nombre')->ignore($grupo?->id)],
            'descripcion'   => 'nullable|string|max:255',
            'integrantes'   => 'nullable|array',
            'integrantes.*' => 'integer|exists:users,id',
        ], [
            'nombre.unique' => 'Ya existe un grupo con ese nombre.',
        ]);
    }

    private function integrantesParaVista($usuarios)
    {
        return $usuarios->map(fn (User $u) => [
            'id'      => $u->id,
            'nombre'  => $u->name,
            'detalle' => collect([$u->puesto_empleado, $u->sede?->nombre])->filter()->implode(' · '),
        ]);
    }
}