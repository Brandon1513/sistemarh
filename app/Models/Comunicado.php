<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    protected $fillable = [
        'tipo', 'categoria', 'titulo', 'resumen', 'contenido', 'imagen', 'enlace',
        'autor_id', 'firma',
        'estado', 'revisor_id', 'comentario_revision', 'revisado_en', 'publicar_en', 'publicado_en', 'fijado',
        'enviar_push', 'enviar_correo', 'remitente',
    ];

    protected function casts(): array
    {
        return [
            'revisado_en'  => 'datetime',
            'publicar_en'  => 'datetime',
            'publicado_en' => 'datetime',
            'fijado'       => 'boolean',
            'enviar_push'  => 'boolean',
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
     * El resumen escrito, o uno automático con el inicio del contenido.
     */
    public function getResumenCortoAttribute(): string
    {
        if ($this->resumen) {
            return $this->resumen;
        }

        $texto = html_entity_decode(strip_tags((string) $this->contenido), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', $texto)), 170);
    }
}