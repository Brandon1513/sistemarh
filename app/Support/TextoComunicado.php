<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

class TextoComunicado
{
    /**
     * Texto plano a HTML: párrafos separados por línea en blanco, saltos de línea
     * y **negritas**. Todo lo demás se escapa, así que nadie puede meter código.
     */
    public static function html(?string $texto): HtmlString
    {
        $parrafos = preg_split("/\R\s*\R/u", trim((string) $texto)) ?: [];

        $html = collect($parrafos)
            ->filter(fn ($p) => trim($p) !== '')
            ->map(function ($parrafo) {
                $seguro = e(trim($parrafo));
                $seguro = preg_replace('/\*\*(.+?)\*\*/su', '<strong>$1</strong>', $seguro);

                return '<p>' . nl2br($seguro, false) . '</p>';
            })
            ->implode('');

        return new HtmlString($html);
    }
}