<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Models\Department;
use App\Models\Grupo;
use App\Models\Sede;
use App\Models\User;
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

        $usuario = $request->user();
        $tipos = ComunicadoPolicy::tiposDe($usuario);

        if ($request->input('estado') === 'por_revisar') {
            // Los que este usuario tiene que revisar, sean del tipo que sean
            $query = Comunicado::with(['autor', 'destinos'])->withCount('imagenes')
                ->where('estado', 'en_revision')
                ->where('revisor_id', $usuario->id);
        } else {
            $query = Comunicado::with(['autor', 'destinos', 'revisor'])->withCount('imagenes')->whereIn('tipo', $tipos);

            if ($request->filled('estado')) {
                $query->where('estado', $request->input('estado'));
            }

            if ($request->filled('tipo') && in_array($request->input('tipo'), $tipos, true)) {
                $query->where('tipo', $request->input('tipo'));
            }
        }

        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%' . $request->input('buscar') . '%');
        }

        $comunicados = $query->latest('updated_at')->paginate(15)->withQueryString();

        $conteos = Comunicado::whereIn('tipo', $tipos)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $porRevisar = Comunicado::where('estado', 'en_revision')->where('revisor_id', $usuario->id)->count();

        return view('comunicados.admin.index', compact('comunicados', 'tipos', 'conteos', 'porRevisar'));
    }

    public function create(Request $request)
    {
        $tipos = ComunicadoPolicy::tiposDe($request->user());
        $tipo = $request->query('tipo', $tipos[0] ?? '');

        Gate::authorize('create', [Comunicado::class, $tipo]);

        $comunicado = new Comunicado([
            'enviar_correo' => 'automatico',
            'enviar_push' => true,
            'tipo'           => $tipo,
            'firma'          => ComunicadosService::firmaPorDefecto($tipo),
            // Los avisos de RH nuevos vienen con la plantilla activada
            'usar_plantilla' => $tipo === 'aviso',
            'encabezado'     => $tipo === 'aviso' ? 'AVISO IMPORTANTE' : null,
        ]);

        return view('comunicados.admin.form', ['comunicado' => $comunicado, 'tipos' => $tipos] + $this->datosFormulario($comunicado, $request->user()));
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas($request), $this->mensajes());
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

        $comunicado->load(['imagenes', 'destinos', 'revisor']);
        $tipos = ComunicadoPolicy::tiposDe($request->user());

        return view('comunicados.admin.form', ['comunicado' => $comunicado, 'tipos' => $tipos] + $this->datosFormulario($comunicado, $request->user()));
    }

    public function update(Request $request, Comunicado $comunicado)
    {
        Gate::authorize('update', $comunicado);

        $datos = $request->validate($this->reglas($request), $this->mensajes());
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

    // ─── Revisión ────────────────────────────────────────────────────────────

    /** La vista previa para el revisor, con los botones de aprobar y devolver. */
    public function revisar(Comunicado $comunicado)
    {
        Gate::authorize('review', $comunicado);

        $comunicado->load(['imagenes', 'destinos', 'autor']);
        $alcance = $comunicado->destinatarios()->count();

        return view('comunicados.admin.revisar', compact('comunicado', 'alcance'));
    }

    public function aprobar(Comunicado $comunicado)
    {
        Gate::authorize('review', $comunicado);
        $this->comunicados->aprobarRevision($comunicado);
        $comunicado->refresh();

        $mensaje = $comunicado->estado === 'programado'
            ? "Aprobaste «{$comunicado->titulo}»: se publicará el " . $comunicado->publicar_en->locale('es')->isoFormat('D [de] MMMM [a las] H:mm') . ' y le avisamos a su autor.'
            : "Aprobaste «{$comunicado->titulo}»: ya está publicado y le avisamos a su autor.";

        return redirect()->route('comunicados.admin.index', ['estado' => 'por_revisar'])->with('success', $mensaje);
    }

    public function devolver(Request $request, Comunicado $comunicado)
    {
        Gate::authorize('review', $comunicado);

        $datos = $request->validate([
            'comentario' => 'required|string|min:5|max:2000',
        ], [
            'comentario.required' => 'Escribe qué hay que corregir, para que el autor sepa qué cambiar.',
            'comentario.min'      => 'Escribe un poco más de detalle de qué hay que corregir.',
        ]);

        $this->comunicados->devolver($comunicado, $datos['comentario']);

        return redirect()->route('comunicados.admin.index', ['estado' => 'por_revisar'])
            ->with('success', "Devolviste «{$comunicado->titulo}» a su autor con tus comentarios.");
    }

    public function retirar(Comunicado $comunicado)
    {
        Gate::authorize('retirar', $comunicado);
        $this->comunicados->retirarRevision($comunicado);

        return redirect()->route('comunicados.admin.edit', $comunicado)
            ->with('success', 'Lo retiraste de la revisión; volvió a borrador y ya puedes editarlo.');
    }

    // ─── Apoyo al formulario ─────────────────────────────────────────────────

    /**
     * Búsqueda de personas para elegir destinatarios (por nombre, correo o clave de empleado).
     */
    public function buscarPersonas(Request $request)
    {
        Gate::authorize('viewAny', Comunicado::class);

        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $personas = User::with('sede')
            ->where(fn ($w) => $w->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('clave_empleado', 'like', "%{$q}%"))
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn (User $u) => [
                'id'      => $u->id,
                'nombre'  => $u->name,
                'detalle' => collect([$u->puesto_empleado, $u->sede?->nombre])->filter()->implode(' · '),
            ]);

        return response()->json($personas);
    }

    /**
     * Cuántas personas reciben los destinatarios elegidos (el contador del formulario).
     */
    public function contarDestinatarios(Request $request)
    {
        Gate::authorize('viewAny', Comunicado::class);

        $destinos = $request->input('para') === 'elegir'
            ? ComunicadosService::leerDestinos((array) $request->input('destinos', []))
            : [];

        if ($request->input('para') === 'elegir' && $destinos === []) {
            return response()->json(['total' => 0, 'correo' => '']);
        }

        return response()->json([
            'total'  => Comunicado::consultaDestinatarios($destinos)->count(),
            'correo' => app(\App\Services\CorreoComunicados::class)->resumen($destinos),
        ]);
    }

    /**
     * Las listas para elegir destinatarios y revisor, y lo que ya tiene elegido el comunicado.
     */
    private function datosFormulario(Comunicado $comunicado, User $usuario): array
    {
        $actuales = $comunicado->exists
            ? $comunicado->destinos->map(fn ($d) => "{$d->tipo}:{$d->destino_id}")->all()
            : [];

        // Si el formulario regresó con errores, se respeta lo que la persona había marcado
        $elegidos = old('destinos', $actuales);

        $idsPersonas = collect($elegidos)
            ->filter(fn ($v) => str_starts_with((string) $v, 'usuario:'))
            ->map(fn ($v) => (int) substr($v, 8));

        $idsGrupos = collect($elegidos)
            ->filter(fn ($v) => str_starts_with((string) $v, 'grupo:'))
            ->map(fn ($v) => (int) substr($v, 6));

        return [
            'sedes'         => Sede::orderBy('nombre')->get(['id', 'nombre']),
            'departamentos' => Department::orderBy('name')->get(['id', 'name']),
            // Los grupos activos, más los inactivos que este comunicado ya tenía elegidos
            'grupos'        => Grupo::withCount('usuarios')
                ->where(fn ($q) => $q->where('activo', true)->orWhereIn('id', $idsGrupos))
                ->orderBy('nombre')
                ->get(),
            'elegidos'      => $elegidos,
            'para'          => old('para', $actuales ? 'elegir' : 'todos'),
            'personas'      => User::with('sede')->whereIn('id', $idsPersonas)->orderBy('name')->get()
                ->map(fn (User $u) => [
                    'id'      => $u->id,
                    'nombre'  => $u->name,
                    'detalle' => collect([$u->puesto_empleado, $u->sede?->nombre])->filter()->implode(' · '),
                ]),
            'revisores'     => ComunicadosService::revisoresPara($usuario),
        ];
    }

    private function reglas(Request $request): array
{
    $publicando = in_array($request->input('accion'), ['publicar', 'revision'], true);
    $tipo = $request->input('tipo');

    return [
        'tipo'             => ['required', Rule::in(['noticia', 'red_social', 'aviso'])],
        'categoria'        => ['nullable', Rule::in(array_keys(Comunicado::CATEGORIAS_AVISO))],
        'titulo'           => 'required|string|max:255',
        'resumen'          => 'nullable|string|max:300',
        // Para publicar o mandar a revisión hace falta contenido, salvo en redes sociales
        'contenido'        => [Rule::requiredIf($publicando && $tipo !== 'red_social'), 'nullable', 'string', 'max:20000'],
        'enlace'           => [Rule::requiredIf($tipo === 'red_social'), 'nullable', 'url', 'max:255'],
        'firma'            => 'nullable|string|max:100',
        'fijado'           => 'nullable|boolean',
        'enviar_push'      => 'nullable|boolean',
        'enviar_correo'    => 'nullable|boolean',
        'usar_plantilla'   => 'nullable|boolean',
        'encabezado'       => ['nullable', Rule::in(Comunicado::ENCABEZADOS)],
        'imagen'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'imagen_generada'  => 'nullable|image|mimes:png|max:4096',
        'quitar_imagen'    => 'nullable|boolean',
        'galeria'          => 'nullable|array|max:20',
        'galeria.*'        => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        'quitar_galeria'   => 'nullable|array',
        'quitar_galeria.*' => 'integer',
        'para'             => 'required|in:todos,elegir',
        'destinos'         => 'required_if:para,elegir|array',
        'destinos.*'       => ['string', 'regex:/^(sede|departamento|grupo|usuario):\d+$/'],
        'revisor_id'       => 'required_if:accion,revision|nullable|integer',
        'cuando'           => 'nullable|in:ahora,programar',
        // Solo al publicar o mandar a revisión se exige que la fecha sea futura
        'publicar_en'      => ['required_if:cuando,programar', 'nullable', 'date', ...($publicando ? ['after:now'] : [])],
        'accion'           => 'required|in:borrador,publicar,revision',
    ];
}

    private function mensajes(): array
    {
        return [
            'destinos.required_if'    => 'Elige al menos un destinatario, o marca "Para todos".',
            'revisor_id.required_if'  => 'Elige quién de Recursos Humanos lo va a revisar.',
            'publicar_en.required_if' => 'Elige la fecha y hora en que se va a publicar.',
            'publicar_en.after'       => 'La fecha de publicación tiene que ser en el futuro.',
        ];
    }

   private function despuesDeGuardar(Comunicado $comunicado, string $accion)
    {
        $comunicado->refresh()->load('revisor');

        return match (true) {
            $comunicado->estado === 'programado' => redirect()->route('comunicados.admin.index')
                ->with('success', "«{$comunicado->titulo}» se publicará el " . $comunicado->publicar_en->locale('es')->isoFormat('D [de] MMMM [a las] H:mm') . '.'),

            $comunicado->estado === 'publicado' && $accion === 'publicar' => redirect()->route('comunicados.admin.index')
                ->with('success', "«{$comunicado->titulo}» ya está publicado."),

            $accion === 'revision' => redirect()->route('comunicados.admin.index')
                ->with('success', "«{$comunicado->titulo}» se envió a revisión con {$comunicado->revisor?->name}. Te avisaremos por correo."),

            default => redirect()->route('comunicados.admin.edit', $comunicado)
                ->with('success', 'Borrador guardado. Puedes seguir editándolo o publicarlo cuando esté listo.'),
        };
    }
}