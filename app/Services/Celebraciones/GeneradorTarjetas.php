<?php

namespace App\Services\Celebraciones;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Arma las imágenes de cumpleaños, aniversarios y la lista del mes con PHP (GD),
 * a partir de config/celebraciones.php. No necesita navegador: corre solo a las 7:00.
 */
class GeneradorTarjetas
{
    /** ¡Feliz cumpleaños, {nombre}! */
    public function cumpleanos(User $persona, Carbon $dia): string
    {
        return $this->dibujar('cumpleanos', [
            'nombre' => self::primerNombre($persona->name),
        ], $persona, [], "celebraciones/{$dia->format('Y-m')}/cumpleanos-{$persona->id}-{$dia->format('d')}.png");
    }

    /** ¡Felicidades! N años */
    public function aniversario(User $persona, Carbon $dia): string
    {
        $anios = $persona->aniosEnLaEmpresa($dia);

        return $this->dibujar('aniversario', [
            'NOMBRE'        => mb_strtoupper(self::nombreCorto($persona->name)),
            'PUESTO'        => mb_strtoupper((string) $persona->puesto_empleado),
            'ANIOS'         => $anios . ($anios === 1 ? ' AÑO' : ' AÑOS'),
            'FECHA_INGRESO' => $persona->fecha_ingreso?->format('d-m-Y') ?? '',
        ], $persona, [], "celebraciones/{$dia->format('Y-m')}/aniversario-{$persona->id}-{$dia->format('d')}.png");
    }

    /**
     * Cumpleañeros del mes. $secciones = ['TURNO DIURNO' => [['dia' => 3, 'nombre' => ..., 'puesto' => ...], ...], ...]
     */
    public function listaMes(Carbon $mes, array $secciones): string
    {
        return $this->dibujar('lista_mes', [
            'MES' => mb_strtoupper($mes->copy()->locale('es')->isoFormat('MMMM')),
        ], null, $secciones, "celebraciones/{$mes->format('Y-m')}/cumpleaneros-del-mes.png");
    }

    // ─── Nombres ─────────────────────────────────────────────────────────────

    /** "Jocelin Susana Ávila López" → "Jocelin" */
    public static function primerNombre(string $nombre): string
    {
        return Str::of($nombre)->squish()->explode(' ')->first() ?: $nombre;
    }

    /** "Mónica Salas Pérez" → "Mónica Salas" */
    public static function nombreCorto(string $nombre): string
    {
        return Str::of($nombre)->squish()->explode(' ')->take(2)->implode(' ');
    }

    // ─── El dibujo ───────────────────────────────────────────────────────────

    private function dibujar(string $clave, array $datos, ?User $persona, array $secciones, string $ruta): string
    {
        $p = config("celebraciones.plantillas.{$clave}");
        $alto = $this->altoNecesario($p, $secciones);

        $img = $this->lienzo($p, $alto);

        foreach ($p['elementos'] as $e) {
            match ($e['tipo']) {
                'texto'   => $this->texto($img, $this->reemplazar($e['texto'], $datos), $e),
                'parrafo' => $this->parrafo($img, $this->reemplazar($e['texto'], $datos), $e),
                'foto'    => $this->foto($img, $persona, $e),
                'lista'   => $this->lista($img, $secciones, $e),
            };
        }

        ob_start();
        imagepng($img, null, 6);
        $png = ob_get_clean();
        imagedestroy($img);

        Storage::disk('public')->put($ruta, $png);

        return $ruta;
    }

    /** Si la lista del mes es larga, la imagen crece para que quepa completa. */
    private function altoNecesario(array $p, array $secciones): int
    {
        foreach ($p['elementos'] as $e) {
            if ($e['tipo'] === 'lista') {
                $filas = collect($secciones)->sum(fn ($s) => count($s));
                $necesario = $e['y'] + count($secciones) * ($e['alto_fila'] * 2.6) + $filas * $e['alto_fila'] + 120;

                return (int) max($p['alto'], $necesario);
            }
        }

        return $p['alto'];
    }

    private function lienzo(array $p, int $alto): \GdImage
    {
        $img = imagecreatetruecolor($p['ancho'], $alto);
        imagealphablending($img, true);

        $ruta = resource_path($p['fondo']);

        if (is_file($ruta)) {
            $fondo = imagecreatefromstring(file_get_contents($ruta));
            $altoFondo = (int) round(imagesy($fondo) * $p['ancho'] / imagesx($fondo));

            // Si la imagen es más alta que el fondo (lista larga), lo que sobra se pinta del color de abajo del fondo
            imagefilledrectangle($img, 0, 0, $p['ancho'], $alto, imagecolorat($fondo, 5, imagesy($fondo) - 5));
            imagecopyresampled($img, $fondo, 0, 0, 0, 0, $p['ancho'], $altoFondo, imagesx($fondo), imagesy($fondo));
            imagedestroy($fondo);
        } else {
            $this->fondoProvisional($img, $p['provisional'] ?? 'crema');
        }

        return $img;
    }

    /** Mientras llegan los de marketing: crema con confeti, banderines y el logo. */
    private function fondoProvisional(\GdImage $img, string $estilo): void
    {
        $w = imagesx($img);
        $h = imagesy($img);

        imagefilledrectangle($img, 0, 0, $w, $h, $this->color($img, $estilo === 'morado' ? '#EBDDF0' : '#F4EFE8'));

        // Confeti suave (siempre el mismo, para que la imagen no cambie cada vez)
        mt_srand(7);
        $tonos = ['#BBA4C0', '#EED39B', '#F0B3C3', '#B8DFF0'];
        for ($i = 0; $i < 45; $i++) {
            imagefilledellipse($img, mt_rand(0, $w), mt_rand(220, $h), 18, 18, $this->color($img, $tonos[$i % 4], 60));
        }

        // El arco morado del aniversario
        if ($estilo === 'morado') {
            $morado = $this->color($img, 'morado');
            imagefilledellipse($img, (int) ($w / 2), 560, $w - 120, 1000, $morado);
            imagefilledrectangle($img, 60, 560, $w - 60, $h - 260, $morado);
        }

        // Banderines
        $colores = ['morado', 'dorado', 'rosa', '#9B4DAB'];
        $cuerda = $this->color($img, 'dorado');
        imagesetthickness($img, 3);
        $anterior = null;
        for ($i = 0; $i <= 11; $i++) {
            $x = (int) ($i * $w / 11);
            $y = 30 + (int) (70 * sin(M_PI * $i / 11));
            if ($anterior) {
                imageline($img, $anterior[0], $anterior[1], $x, $y, $cuerda);
            }
            imagefilledpolygon($img, [$x - 32, $y, $x + 32, $y, $x, $y + 80], $this->color($img, $colores[$i % 4]));
            $anterior = [$x, $y];
        }
        imagesetthickness($img, 1);

        // Logo
        $logo = public_path('images/logo.png');
        if (is_file($logo)) {
            $l = imagecreatefromstring(file_get_contents($logo));
            imagecopyresampled($img, $l, (int) ($w / 2 - 110), 110, 0, 0, 220, 220, imagesx($l), imagesy($l));
            imagedestroy($l);
        }
    }

    private function texto(\GdImage $img, string $texto, array $e): void
    {
        $fuente = $this->fuente($e['fuente']);
        $tamano = $e['tamano'];
        $maximo = $e['ancho'] ?? imagesx($img) - 120;

        // Si un nombre o puesto es muy largo, se achica hasta que quepa
        while ($tamano > 12 && $this->anchoTexto($texto, $fuente, $tamano) > $maximo) {
            $tamano -= 2;
        }

        $ancho = $this->anchoTexto($texto, $fuente, $tamano);
        $x = match ($e['alineacion'] ?? 'centro') {
            'izquierda' => $e['x'] ?? 60,
            'derecha'   => ($e['x'] ?? imagesx($img) - 60) - $ancho,
            default     => (int) (($e['x'] ?? imagesx($img) / 2) - $ancho / 2),
        };

        imagettftext($img, $tamano, 0, (int) $x, (int) $e['y'], $this->color($img, $e['color']), $fuente, $texto);
    }

    private function parrafo(\GdImage $img, string $texto, array $e): void
    {
        $fuente = $this->fuente($e['fuente']);
        $maximo = $e['ancho'] ?? imagesx($img) - 160;

        $lineas = [];
        $linea = '';
        foreach (preg_split('/\s+/u', trim($texto)) as $palabra) {
            $prueba = trim("{$linea} {$palabra}");
            if ($linea !== '' && $this->anchoTexto($prueba, $fuente, $e['tamano']) > $maximo) {
                $lineas[] = $linea;
                $linea = $palabra;
            } else {
                $linea = $prueba;
            }
        }
        if ($linea !== '') {
            $lineas[] = $linea;
        }

        $y = $e['y'];
        foreach ($lineas as $l) {
            // ancho enorme: en un párrafo no se achica la letra, se parte en renglones
            $this->texto($img, $l, ['y' => $y, 'ancho' => 99999] + $e);
            $y += (int) round($e['tamano'] * 1.6);
        }
    }

    private function foto(\GdImage $img, ?User $persona, array $e): void
    {
        [$x, $y, $w, $h] = [$e['x'], $e['y'], $e['ancho'], $e['alto']];
        $circulo = ($e['forma'] ?? 'rectangulo') === 'circulo';
        $grosor = $e['grosor'] ?? 0;

        // El borde va detrás de la foto
        if ($grosor) {
            $borde = $this->color($img, $e['borde'] ?? 'blanco');
            $circulo
                ? imagefilledellipse($img, (int) ($x + $w / 2), (int) ($y + $h / 2), $w + 2 * $grosor, $h + 2 * $grosor, $borde)
                : imagefilledrectangle($img, $x - $grosor, $y - $grosor, $x + $w + $grosor - 1, $y + $h + $grosor - 1, $borde);
        }

        $recorte = imagecreatetruecolor($w, $h);
        imagesavealpha($recorte, true);
        $foto = $this->cargarFoto($persona);

        if ($foto) {
            // Rellenar el espacio sin deformar (como "cover"), un poco cargado hacia arriba, donde está la cara
            $escala = max($w / imagesx($foto), $h / imagesy($foto));
            $cw = (int) ($w / $escala);
            $ch = (int) ($h / $escala);
            $cx = (int) ((imagesx($foto) - $cw) / 2);
            $cy = (int) ((imagesy($foto) - $ch) * 0.3);
            imagecopyresampled($recorte, $foto, 0, 0, $cx, $cy, $w, $h, $cw, $ch);
            imagedestroy($foto);
        } else {
            // Sin foto oficial: sus iniciales
            imagefilledrectangle($recorte, 0, 0, $w, $h, $this->color($recorte, 'lila'));
            $iniciales = collect(explode(' ', self::nombreCorto($persona?->name ?? 'D')))
                ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
            $fuente = $this->fuente('grande');
            $tam = (int) ($h * 0.32);
            $b = imagettfbbox($tam, 0, $fuente, $iniciales);
            imagettftext($recorte, $tam, 0, (int) (($w - ($b[2] - $b[0])) / 2), (int) (($h + $tam) / 2),
                $this->color($recorte, 'blanco'), $fuente, $iniciales);
        }

        // Recorte en círculo: lo de fuera se vuelve transparente
        if ($circulo) {
            imagealphablending($recorte, false);
            $transparente = imagecolorallocatealpha($recorte, 0, 0, 0, 127);
            $rx = $w / 2;
            $ry = $h / 2;
            for ($py = 0; $py < $h; $py++) {
                for ($px = 0; $px < $w; $px++) {
                    if ((($px - $rx) ** 2) / ($rx ** 2) + (($py - $ry) ** 2) / ($ry ** 2) > 1) {
                        imagesetpixel($recorte, $px, $py, $transparente);
                    }
                }
            }
        }

        imagecopy($img, $recorte, $x, $y, 0, 0, $w, $h);
        imagedestroy($recorte);
    }

    /** La foto oficial, enderezada si viene girada del celular. */
    private function cargarFoto(?User $persona): ?\GdImage
    {
        if (! $persona?->foto_oficial || ! Storage::disk('public')->exists($persona->foto_oficial)) {
            return null;
        }

        $ruta = Storage::disk('public')->path($persona->foto_oficial);
        $foto = @imagecreatefromstring(file_get_contents($ruta));
        if (! $foto) {
            return null;
        }

        if (function_exists('exif_read_data') && preg_match('/\.jpe?g$/i', $ruta)) {
            $orientacion = @exif_read_data($ruta)['Orientation'] ?? 1;
            $foto = match ($orientacion) {
                3       => imagerotate($foto, 180, 0),
                6       => imagerotate($foto, -90, 0),
                8       => imagerotate($foto, 90, 0),
                default => $foto,
            };
        }

        return $foto;
    }

    private function lista(\GdImage $img, array $secciones, array $e): void
    {
        $y = $e['y'];
        $fuente = $this->fuente($e['fuente']);
        $fuenteTitulo = $this->fuente($e['titulo_fuente']);
        $color = $this->color($img, $e['color']);
        $colorTitulo = $this->color($img, $e['titulo_color']);
        $c = $e['columnas'];

        foreach ($secciones as $titulo => $filas) {
            // Título de la sección, centrado y subrayado
            $y += (int) ($e['alto_fila'] * 1.4);
            $b = imagettfbbox($e['titulo_tamano'], 0, $fuenteTitulo, $titulo);
            $ancho = $b[2] - $b[0];
            $xt = (int) ((imagesx($img) - $ancho) / 2);
            imagettftext($img, $e['titulo_tamano'], 0, $xt, $y, $colorTitulo, $fuenteTitulo, $titulo);
            imagefilledrectangle($img, $xt, $y + 6, $xt + $ancho, $y + 8, $colorTitulo);
            $y += (int) ($e['alto_fila'] * 1.2);

            foreach ($filas as $fila) {
                imagettftext($img, $e['tamano'], 0, $c['dia'], $y, $color, $fuente, (string) $fila['dia']);
                imagettftext($img, $e['tamano'], 0, $c['nombre'], $y, $color, $fuente, Str::limit($fila['nombre'], 34, '…'));
                imagettftext($img, $e['tamano'], 0, $c['puesto'], $y, $color, $fuente, Str::limit($fila['puesto'], 34, '…'));
                $y += $e['alto_fila'];
            }
        }
    }

    // ─── Ayudas ──────────────────────────────────────────────────────────────

    private function reemplazar(string $texto, array $datos): string
    {
        return strtr($texto, collect($datos)->mapWithKeys(fn ($v, $k) => ['{' . $k . '}' => $v])->all());
    }

    private function fuente(string $clave): string
    {
        $ruta = resource_path(config("celebraciones.fuentes.{$clave}"));

        if (! is_file($ruta)) {
            throw new RuntimeException("Falta la fuente «{$clave}» en {$ruta}");
        }

        return $ruta;
    }

    private function anchoTexto(string $texto, string $fuente, int $tamano): int
    {
        $b = imagettfbbox($tamano, 0, $fuente, $texto);

        return abs($b[2] - $b[0]);
    }

    /** Un color por nombre (de la config) o en #RRGGBB. $alfa: 0 opaco … 127 transparente. */
    private function color(\GdImage $img, string $color, int $alfa = 0): int
    {
        $hex = ltrim(config("celebraciones.colores.{$color}", $color), '#');

        return imagecolorallocatealpha($img, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)), $alfa);
    }
}