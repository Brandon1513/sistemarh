<?php

namespace App\Policies;

use App\Models\Comunicado;
use App\Models\User;

class ComunicadoPolicy
{
    /** Qué tipos de comunicado publica cada rol. El administrador puede todos. */
    public const TIPOS_POR_ROL = [
        'marketing'        => ['noticia', 'red_social'],
        'recursos_humanos' => ['aviso'],
    ];

    /** Los tipos de comunicado que administra cada rol. */
    public static function tiposDe(User $user): array
    {
        if ($user->hasRole('administrador')) {
            return ['noticia', 'red_social', 'aviso', 'cumpleanos', 'aniversario'];
        }

        $tipos = [];

        if ($user->hasRole('marketing')) {
            array_push($tipos, 'noticia', 'red_social');
        }

        // RH: sus avisos y las celebraciones (cumpleaños, aniversarios, lista del mes e invitaciones)
        if ($user->hasRole('recursos_humanos')) {
            array_push($tipos, 'aviso', 'cumpleanos', 'aniversario');
        }

        return array_values(array_unique($tipos));
    }

    /** Ver el panel de comunicados (la lista para administrarlos). */
    public function viewAny(User $user): bool
    {
        return self::tiposDe($user) !== [];
    }

    public function create(User $user, string $tipo): bool
    {
        return in_array($tipo, self::tiposDe($user), true);
    }

    /**
     * Editar: de un tipo que le corresponda, y que no esté esperando revisión
     * (mientras alguien lo revisa, no se le mueve).
     */
    public function update(User $user, Comunicado $comunicado): bool
    {
        return in_array($comunicado->tipo, self::tiposDe($user), true)
            && $comunicado->estado !== 'en_revision';
    }

    public function delete(User $user, Comunicado $comunicado): bool
    {
        return in_array($comunicado->tipo, self::tiposDe($user), true);
    }

    /** Revisar: solo RH, y solo la persona que eligieron como revisora (fase 2). */
    public function review(User $user, Comunicado $comunicado): bool
    {
        return $user->hasRole('recursos_humanos')
            && $comunicado->estado === 'en_revision'
            && $comunicado->revisor_id === $user->id;
    }
    /** Retirar de la revisión: quien administra ese tipo, mientras siga en revisión. */
    public function retirar(User $user, Comunicado $comunicado): bool
    {
        return in_array($comunicado->tipo, self::tiposDe($user), true)
            && $comunicado->estado === 'en_revision';
    }
}
