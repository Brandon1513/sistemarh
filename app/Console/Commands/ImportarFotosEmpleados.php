<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Sube de un jalón las fotos oficiales de una carpeta. Cada foto se relaciona con su empleado
 * por el nombre del archivo: la clave ("0005.jpg"), el nombre ("ESQUIVIAS ANDALON MARIA.jpg")
 * o los dos ("0005 ESQUIVIAS ANDALON MARIA.jpg").
 *
 * Sin --aplicar solo SIMULA. Al final deja un reporte con lo que se relacionó y quién se quedó sin foto.
 */
class ImportarFotosEmpleados extends Command
{
    protected $signature = 'empleados:importar-fotos
        {carpeta : Carpeta con las fotos (jpg, jpeg, png o webp)}
        {--aplicar : Guardar las fotos. Sin esto, solo simula}
        {--reemplazar : Cambiar también la foto de quien ya tiene una}';

    protected $description = 'Sube las fotos oficiales de una carpeta, relacionándolas por clave o nombre del archivo';

    /** La foto se guarda achicada a este tamaño máximo (sobra para las tarjetas de 1545 px de ancho) */
    private const LADO_MAXIMO = 1200;

    private const EXTENSIONES = ['jpg', 'jpeg', 'png', 'webp'];

    public function handle(): int
    {
        $carpeta = rtrim($this->argument('carpeta'), '/');

        if (! is_dir($carpeta)) {
            $this->error("No encuentro la carpeta: {$carpeta}");

            return self::FAILURE;
        }

        if (! function_exists('imagecreatefromstring')) {
            $this->error('PHP no tiene GD: no se pueden procesar las fotos.');

            return self::FAILURE;
        }

        $aplicar = (bool) $this->option('aplicar');
        $reemplazar = (bool) $this->option('reemplazar');

        $archivos = collect(File::allFiles($carpeta))
            ->filter(fn ($a) => in_array(strtolower($a->getExtension()), self::EXTENSIONES, true))
            ->values();

        $this->info(($aplicar ? 'SUBIENDO' : 'SIMULANDO (no se guarda nada)') . ": {$archivos->count()} fotos en la carpeta.");

        $usuarios = User::orderBy('name')->get(); // solo activos
        $porClave = $usuarios->filter(fn ($u) => filled($u->clave_empleado))
            ->groupBy(fn ($u) => ImportarEmpleadosRh::clave($u->clave_empleado));

        $reporte = [];
        $asignadas = []; // id de usuario → archivo (para detectar dos fotos de la misma persona)

        foreach ($archivos as $archivo) {
            $nombreArchivo = $archivo->getFilename();
            [$usuario, $como, $problema] = $this->relacionar($archivo->getFilenameWithoutExtension(), $usuarios, $porClave);

            $fila = ['archivo' => $nombreArchivo, 'resultado' => '', 'id' => '', 'clave' => '', 'nombre' => '', 'como' => $como ?? '', 'aviso' => $problema ?? ''];

            if (! $usuario) {
                $fila['resultado'] = 'sin relacionar';
                $reporte[] = $fila;
                continue;
            }

            $fila['id'] = $usuario->id;
            $fila['clave'] = $usuario->clave_empleado;
            $fila['nombre'] = $usuario->name;

            if (isset($asignadas[$usuario->id])) {
                $fila['resultado'] = 'repetida';
                $fila['aviso'] = "Esta persona ya tiene otra foto en la carpeta: {$asignadas[$usuario->id]}";
                $reporte[] = $fila;
                continue;
            }
            $asignadas[$usuario->id] = $nombreArchivo;

            $teniaFoto = filled($usuario->foto_oficial);

            if ($teniaFoto && ! $reemplazar) {
                $fila['resultado'] = 'ya tenía foto';
                $fila['aviso'] = 'No se cambió (usa --reemplazar para cambiarla)';
                $reporte[] = $fila;
                continue;
            }

            $jpg = $this->preparar($archivo->getPathname());
            if ($jpg === null) {
                $fila['resultado'] = 'error';
                $fila['aviso'] = 'No se pudo leer la imagen (¿archivo dañado o formato raro?)';
                $reporte[] = $fila;
                continue;
            }

            if ($aplicar) {
                $ruta = 'empleados/fotos/' . Str::slug($usuario->clave_empleado ?: 'id-' . $usuario->id) . '-' . Str::random(6) . '.jpg';
                Storage::disk('public')->put($ruta, $jpg);

                if ($usuario->foto_oficial) {
                    Storage::disk('public')->delete($usuario->foto_oficial);
                }
                $usuario->forceFill(['foto_oficial' => $ruta])->save();
            }

            $fila['resultado'] = $teniaFoto ? 'reemplazada' : 'subida';
            $reporte[] = $fila;
        }

        $this->resumen($reporte, $aplicar);

        return self::SUCCESS;
    }

    /**
     * Encuentra al empleado de un archivo. Regresa [usuario, cómo, problema].
     */
    private function relacionar(string $nombreArchivo, Collection $usuarios, Collection $porClave): array
    {
        // "0005 ESQUIVIAS ANDALON MARIA" → clave 0005 y nombre "ESQUIVIAS ANDALON MARIA"
        $limpio = trim(preg_replace('/[_\-\.]+/', ' ', $nombreArchivo));
        preg_match('/^\s*0*(\d{1,6})\b/', $limpio, $m);
        $clave = $m[1] ?? null;
        $texto = trim(preg_replace('/^\s*\d+\s*/', '', $limpio));

        // 1. Por clave (y si también trae nombre, que se parezca)
        if ($clave !== null) {
            $candidatos = $porClave[ImportarEmpleadosRh::clave($clave)] ?? collect();

            if ($texto !== '') {
                $candidatos = $candidatos->filter(fn ($u) => ImportarEmpleadosRh::seParecen($u->name, $texto));
            }

            if ($candidatos->count() === 1) {
                return [$candidatos->first(), 'clave', null];
            }
            if ($candidatos->count() > 1) {
                return [null, null, "Hay varios empleados con la clave {$clave}"];
            }
            if ($texto === '') {
                return [null, null, "No hay ningún empleado activo con la clave {$clave}"];
            }
        }

        // 2. Por nombre: todas las palabras del archivo deben estar en el nombre del empleado
        $palabras = ImportarEmpleadosRh::tokens($texto);
        if (count($palabras) < 2) {
            return [null, null, 'El nombre del archivo no tiene clave ni suficiente nombre para reconocerlo'];
        }

        $exactos = $usuarios->filter(fn ($u) => ! array_diff($palabras, ImportarEmpleadosRh::tokens($u->name)));
        if ($exactos->count() === 1) {
            return [$exactos->first(), 'nombre', null];
        }
        if ($exactos->count() > 1) {
            return [null, null, 'El nombre coincide con varios empleados: ' . $exactos->pluck('name')->implode(', ')];
        }

        // 3. El nombre corto de la intranet viene completo dentro del archivo:
        //    "Omar Arellano" en "ARELLANO DELGADO OMAR GUADALUPE"
        $contenidos = $usuarios->filter(function ($u) use ($palabras) {
            $delUsuario = ImportarEmpleadosRh::tokens($u->name);

            return count($delUsuario) >= 2 && ! array_diff($delUsuario, $palabras);
        });
        if ($contenidos->count() === 1) {
            return [$contenidos->first(), 'nombre parecido', 'En la intranet tiene un nombre más corto: confirma que es la misma persona'];
        }
        if ($contenidos->count() > 1) {
            return [null, null, 'El nombre coincide con varios empleados: ' . $contenidos->pluck('name')->implode(', ')];
        }

        // 4. Por nombre parecido (comparten al menos 3 palabras)
        $parecidos = $usuarios->filter(fn ($u) => ImportarEmpleadosRh::seParecen($u->name, $texto)
            && count(array_intersect($palabras, ImportarEmpleadosRh::tokens($u->name))) >= 3);
        if ($parecidos->count() === 1) {
            return [$parecidos->first(), 'nombre parecido', 'Confirma que es la misma persona'];
        }

        return [null, null, $parecidos->count() > 1
            ? 'Se parece a varios empleados: ' . $parecidos->pluck('name')->implode(', ')
            : 'No encontré a nadie con ese nombre (¿baja, o escrito distinto?)'];
    }

    /** Endereza la foto (las de celular vienen giradas), la achica y la regresa como JPG. */
    private function preparar(string $ruta): ?string
    {
        $img = @imagecreatefromstring(file_get_contents($ruta));
        if (! $img) {
            return null;
        }

        if (function_exists('exif_read_data') && preg_match('/\.jpe?g$/i', $ruta)) {
            $orientacion = @exif_read_data($ruta)['Orientation'] ?? 1;
            $img = match ($orientacion) {
                3       => imagerotate($img, 180, 0),
                6       => imagerotate($img, -90, 0),
                8       => imagerotate($img, 90, 0),
                default => $img,
            };
        }

        $w = imagesx($img);
        $h = imagesy($img);
        $escala = min(1, self::LADO_MAXIMO / max($w, $h));

        $final = imagecreatetruecolor((int) round($w * $escala), (int) round($h * $escala));
        imagefill($final, 0, 0, imagecolorallocate($final, 255, 255, 255)); // fondo blanco si el PNG era transparente
        imagecopyresampled($final, $img, 0, 0, 0, 0, imagesx($final), imagesy($final), $w, $h);
        imagedestroy($img);

        ob_start();
        imagejpeg($final, null, 88);
        imagedestroy($final);

        return ob_get_clean();
    }

    private function resumen(array $reporte, bool $aplicar): void
    {
        $filas = collect($reporte);
        $cuenta = fn ($r) => $filas->where('resultado', $r)->count();

        // Quién se queda sin foto (activos e incluidos en celebraciones)
        $conFotoNueva = $aplicar ? [] : $filas->whereIn('resultado', ['subida', 'reemplazada'])->pluck('id')->all();
        $sinFoto = User::where('incluir_celebraciones', true)
            ->whereNull('foto_oficial')
            ->whereNotIn('id', $conFotoNueva)
            ->with(['departamento', 'sede'])
            ->orderBy('name')
            ->get();

        $this->newLine();
        $this->table(['', 'Fotos'], [
            ['Subidas', $cuenta('subida')],
            ['Reemplazadas', $cuenta('reemplazada')],
            ['Ya tenían foto (no se tocaron)', $cuenta('ya tenía foto')],
            ['Relacionadas por nombre parecido (revisar)', $filas->where('como', 'nombre parecido')->count()],
            ['Sin relacionar', $cuenta('sin relacionar')],
            ['Repetidas (dos fotos de la misma persona)', $cuenta('repetida')],
            ['Con error', $cuenta('error')],
        ]);
        $this->warn("Empleados que se quedan sin foto oficial: {$sinFoto->count()}");

        foreach ($filas->whereIn('resultado', ['sin relacionar', 'repetida', 'error'])->take(20) as $f) {
            $this->line("  {$f['archivo']}: {$f['aviso']}");
        }

        $carpeta = storage_path('app/importaciones');
        File::ensureDirectoryExists($carpeta);
        $sello = now()->format('Ymd-His') . ($aplicar ? '' : '-simulacion');

        // Reporte de las fotos
        $ruta = "{$carpeta}/fotos-{$sello}.csv";
        $f = fopen($ruta, 'w');
        fwrite($f, "\xEF\xBB\xBF");
        fputcsv($f, ['Archivo', 'Resultado', 'ID', 'Clave', 'Nombre en la intranet', 'Relacionada por', 'Aviso']);
        foreach ($filas as $fila) {
            fputcsv($f, array_values($fila));
        }
        fclose($f);

        // Quién falta de foto
        $rutaFaltan = "{$carpeta}/sin-foto-{$sello}.csv";
        $f = fopen($rutaFaltan, 'w');
        fwrite($f, "\xEF\xBB\xBF");
        fputcsv($f, ['Clave', 'Nombre', 'Departamento', 'Sede', 'Puesto', 'Cumpleaños', 'Aniversario']);
        foreach ($sinFoto as $u) {
            fputcsv($f, [
                $u->clave_empleado, $u->name, $u->departamento?->name, $u->sede?->nombre, $u->puesto_empleado,
                $u->fecha_nacimiento?->locale('es')->isoFormat('D [de] MMMM'),
                $u->fecha_ingreso?->locale('es')->isoFormat('D [de] MMMM'),
            ]);
        }
        fclose($f);

        $this->newLine();
        $this->info("Reporte de las fotos: {$ruta}");
        $this->info("Quién falta de foto:  {$rutaFaltan}");
        $this->line($aplicar
            ? 'Listo, las fotos se guardaron.'
            : 'Fue una SIMULACIÓN: no se guardó nada. Si todo está bien, corre otra vez con --aplicar.');
    }
}
