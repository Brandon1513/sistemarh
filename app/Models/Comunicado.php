<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Comunicado extends Model
{
    use SoftDeletes;

    public const TIPOS = [
        'noticia'    => 'Noticia',
        'red_social' => 'Red social',
        'aviso'      => 'Aviso de RH',
        'cumpleanos' => 'Cumpleaños',
    ];

    public const CATEGORIAS_AVISO = [
        'general'   => 'General',
        'nomina'    => 'Nómina',
        'aguinaldo' => 'Aguinaldo',
        'ptu'       => 'PTU',
        'beneficio' => 'Beneficios',
    ];

    public const ESTADOS = [
        'borrador'    => 'Borrador',
        'en_revision' => 'En revisión',
        'devuelto'    => 'Devuelto',
        'programado'  => 'Programado',
        'publicado'   => 'Publicado',
        'archivado'   => 'Archivado',
    ];

    /** El letrero rojo de la plantilla de avisos de RH. */
    public const ENCABEZADOS = [
        'AVISO IMPORTANTE',
        'COMUNICADO',
        'RECORDATORIO',
        'FELICIDADES',
        'BIENVENIDA',
    ];

    protected $fillable = [
        'tipo', 'categoria', 'titulo', 'resumen', 'contenido', 'imagen', 'usar_plantilla', 'encabezado', 'enlace',
        'autor_id', 'firma',
        'estado', 'revisor_id', 'comentario_revision', 'revisado_en', 'publicar_en', 'publicado_en', 'fijado',
        'enviar_push', 'enviar_correo', 'remitente',
    ];

    protected function casts(): array
    {
        return [
            'usar_plantilla' => 'boolean',
            'revisado_en'    => 'datetime',
            'publicar_en'    => 'datetime',
            'publicado_en'   => 'datetime',
            'fijado'         => 'boolean',
            'enviar_push'    => 'boolean',
        ];
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisor_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ComunicadoImagen::class)->orderBy('orden');
    }

    public function destinos()
    {
        return $this->hasMany(ComunicadoDestino::class);
    }

    // ─── Consultas ───────────────────────────────────────────────────────────

    /**
     * Lo que ven los empleados: publicado y con fecha de publicación ya cumplida.
     * Los fijados van primero; después, los más recientes.
     */
    public function scopeVisibles(Builder $query): Builder
    {
        return $query->where('estado', 'publicado')
            ->where('publicado_en', '<=', now())
            ->orderByDesc('fijado')
            ->orderByDesc('publicado_en');
    }

    /**
     * Solo lo que le toca a este empleado: lo que es para todos (sin destinos),
     * o lo dirigido a su sede, su departamento, uno de sus grupos o a él directamente.
     */
    public function scopeParaUsuario(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            $q->whereDoesntHave('destinos')
              ->orWhereHas('destinos', function ($d) use ($user) {
                  $d->where(fn ($w) => $w->where('tipo', 'sede')->where('destino_id', $user->sede_id))
                    ->orWhere(fn ($w) => $w->where('tipo', 'departamento')->where('destino_id', $user->departamento_id))
                    ->orWhere(fn ($w) => $w->where('tipo', 'usuario')->where('destino_id', $user->id))
                    ->orWhere(fn ($w) => $w->where('tipo', 'grupo')
                        ->whereIn('destino_id', $user->grupos()->select('grupos.id')));
              });
        });
    }

    /**
     * Los empleados que reciben un conjunto de destinos: [['tipo' => 'sede', 'destino_id' => 2], ...].
     * Sin destinos, son todos los empleados activos. La usa el contador del formulario
     * y, en la fase 3, las push y los correos.
     */
    public static function consultaDestinatarios(array $destinos): Builder
    {
        $usuarios = User::query();
        $porTipo = collect($destinos)->groupBy('tipo')->map(fn ($grupo) => $grupo->pluck('destino_id')->all());

        if ($porTipo->isEmpty()) {
            return $usuarios;
        }

        return $usuarios->where(function ($q) use ($porTipo) {
            if ($ids = $porTipo->get('sede')) {
                $q->orWhereIn('sede_id', $ids);
            }
            if ($ids = $porTipo->get('departamento')) {
                $q->orWhereIn('departamento_id', $ids);
            }
            if ($ids = $porTipo->get('usuario')) {
                $q->orWhereIn('id', $ids);
            }
            if ($ids = $porTipo->get('grupo')) {
                $q->orWhereHas('grupos', fn ($g) => $g->whereIn('grupos.id', $ids));
            }
        });
    }

    /** Los empleados que reciben este comunicado. */
    public function destinatarios(): Builder
    {
        return self::consultaDestinatarios($this->destinos()->get(['tipo', 'destino_id'])->toArray());
    }

    // ─── Ayudas para las vistas ──────────────────────────────────────────────

    public function getTipoNombreAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getEstadoNombreAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }

    /**
     * El resumen escrito, o uno automático con el inicio del contenido (sin los ** de las negritas).
     */
    public function getResumenCortoAttribute(): string
    {
        if ($this->resumen) {
            return $this->resumen;
        }

        $texto = html_entity_decode(strip_tags((string) $this->contenido), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(trim(preg_replace('/\s+/u', ' ', str_replace('**', '', $texto))), 170);
    }

    /** ¿Es para toda la empresa? */
    public function getParaTodosAttribute(): bool
    {
        return $this->relationLoaded('destinos')
            ? $this->destinos->isEmpty()
            : ! $this->destinos()->exists();
    }

    /**
     * A quién va, en corto, para el panel: "Todos", "CDMX · Producción", "Comité de seguridad · 2 personas".
     */
    public function getEtiquetaDestinosAttribute(): string
    {
        $destinos = $this->relationLoaded('destinos') ? $this->destinos : $this->destinos()->get();

        if ($destinos->isEmpty()) {
            return 'Todos';
        }

        // Los nombres se buscan una sola vez por petición, aunque la lista tenga muchos comunicados
        static $nombres = null;
        $nombres ??= [
            'sede'         => Sede::pluck('nombre', 'id'),
            'departamento' => Department::pluck('name', 'id'),
            'grupo'        => Grupo::pluck('nombre', 'id'),
        ];

        $partes = $destinos->where('tipo', '!=', 'usuario')
            ->map(fn ($d) => $nombres[$d->tipo][$d->destino_id] ?? null)
            ->filter()
            ->values();

        $personas = $destinos->where('tipo', 'usuario')->count();
        if ($personas) {
            $partes->push($personas === 1 ? '1 persona' : "{$personas} personas");
        }

        $texto = $partes->take(3)->implode(' · ');

        return $partes->count() > 3 ? $texto . ' + ' . ($partes->count() - 3) : $texto;
    }
}