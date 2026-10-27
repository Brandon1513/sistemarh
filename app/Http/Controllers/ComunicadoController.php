<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Policies\ComunicadoPolicy;
use App\Services\ComunicadosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Panel donde marketing y RH administran sus comunicados.
 */
class ComunicadoController extends Controller
{
    public function __construct(private ComunicadosService $comunicados)
    {
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Comunicado::class);

        // Cada quien solo ve los tipos que le tocan
        $tipos = ComunicadoPolicy::tiposDe($request->user());

        $query = Comunicado::with('autor')->withCount('imagenes')->whereIn('tipo', $tipos);

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('tipo') && in_array($request->input('tipo'), $tipos, true)) {
            $query->where('tipo', $request->input('tipo'));
        }

        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->input('buscar') . '%');
        }

        $comunicados = $query->latest('updated_at')->paginate(15)->withQueryString();

        $conteos = Comunicado::whereIn('tipo', $tipos)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return view('comunicados.admin.index', compact('comunicados', 'tipos', 'conteos'));
    }

    public function create(Request $request)
    {
        $tipos = ComunicadoPolicy::tiposDe($request->user());
        $tipo = $request->query('tipo', $tipos[0] ?? '');

        Gate::authorize('create', [Comunicado::class, $tipo]);

        $comunicado = new Comunicado([
            'tipo'           => $tipo,
            'firma'          => ComunicadosService::firmaPorDefecto($tipo),
            // Los avisos de RH nuevos vienen con la plantilla activada
            'usar_plantilla' => $tipo === 'aviso',
            'encabezado'     => $tipo === 'aviso' ? 'AVISO IMPORTANTE' : null,
        ]);

        return view('comunicados.admin.form', compact('comunicado', 'tipos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas($request));
        Gate::authorize('create', [Comunicado::class, $datos['tipo']]);

        $comunicado = $this->comunicados->guardar(
            new Comunicado(['autor_id' => $request->user()->id]),
            $datos,
            $request,
        );

        return $this->despuesDeGuardar($comunicado, $datos['accion']);
    }

    public function edit(Request $request, Comunicado $comunicado)
    {
        Gate::authorize('update', $comunicado);

        $comunicado->load('imagenes');
        $tipos = ComunicadoPolicy::tiposDe($request->user());

        return view('comunicados.admin.form', compact('comunicado', 'tipos'));
    }

    public function update(Request $request, Comunicado $comunicado)
    {
        Gate::authorize('update', $comunicado);

        $datos = $request->validate($this->reglas($request));
        $datos['tipo'] = $comunicado->tipo; // el tipo no cambia después de crearlo

        $this->comunicados->guardar($comunicado, $datos, $request);

        return $this->despuesDeGuardar($comunicado, $datos['accion']);
    }

    public function publicar(Comunicado $comunicado)
    {
        Gate::authorize('update', $comunicado);
        $this->comunicados->publicar($comunicado);

        return back()->with('success', "«{$comunicado->titulo}» ya está publicado.");
    }

    public function archivar(Comunicado $comunicado)
    {
        Gate::authorize('update', $comunicado);
        $this->comunicados->archivar($comunicado);

        return back()->with('success', "«{$comunicado->titulo}» se archivó; ya no lo ven los empleados.");
    }

    public function destroy(Comunicado $comunicado)
    {
        Gate::authorize('delete', $comunicado);
        $comunicado->delete();

        return redirect()->route('comunicados.admin.index')->with('success', "«{$comunicado->titulo}» se eliminó.");
    }

    private function reglas(Request $request): array
    {
        $publicando = $request->input('accion') === 'publicar';
        $tipo = $request->input('tipo');

        return [
            'tipo'             => ['required', Rule::in(['noticia', 'red_social', 'aviso'])],
            'categoria'        => ['nullable', Rule::in(array_keys(Comunicado::CATEGORIAS_AVISO))],
            'titulo'           => 'required|string|max:255',
            'resumen'          => 'nullable|string|max:300',
            // Para publicar hace falta contenido, salvo en redes sociales, que se apoyan en su liga
            'contenido'        => [Rule::requiredIf($publicando && $tipo !== 'red_social'), 'nullable', 'string', 'max:20000'],
            'enlace'           => [Rule::requiredIf($tipo === 'red_social'), 'nullable', 'url', 'max:255'],
            'firma'            => 'nullable|string|max:100',
            'fijado'           => 'nullable|boolean',
            'usar_plantilla'   => 'nullable|boolean',
            'encabezado'       => ['nullable', Rule::in(Comunicado::ENCABEZADOS)],
            'imagen'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'imagen_generada'  => 'nullable|image|mimes:png|max:4096',
            'quitar_imagen'    => 'nullable|boolean',
            'galeria'          => 'nullable|array|max:20',
            'galeria.*'        => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'quitar_galeria'   => 'nullable|array',
            'quitar_galeria.*' => 'integer',
            'accion'           => 'required|in:borrador,publicar',
        ];
    }

    private function despuesDeGuardar(Comunicado $comunicado, string $accion)
    {
        if ($accion === 'publicar') {
            return redirect()->route('comunicados.admin.index')
                ->with('success', "«{$comunicado->titulo}» ya está publicado.");
        }

        return redirect()->route('comunicados.admin.edit', $comunicado)
            ->with('success', 'Borrador guardado. Puedes seguir editándolo o publicarlo cuando esté listo.');
    }
}