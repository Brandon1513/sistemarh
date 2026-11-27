<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Carga la plantilla de empleados de RH: actualiza a los que ya están en la intranet
 * y registra a los que faltan.
 *
 * Sin --aplicar solo SIMULA: hace todo dentro de una transacción y al final la deshace,
 * así el reporte es exacto (incluye errores de la base de datos) y no cambia nada.
 */
class ImportarEmpleadosRh extends Command
{
    protected $signature = 'empleados:importar-rh
        {archivo : CSV (UTF-8) con la plantilla de RH}
        {--aplicar : Guardar los cambios. Sin esto, solo simula}
        {--crear-departamentos : Crear los departamentos que no existan en la intranet}
        {--actualizar-jefes : Cambiar también el jefe de quienes ya están registrados}';

    protected $description = 'Importa la plantilla de empleados de RH (nacimiento, ingreso, puesto, turno y altas nuevas)';

    /** Encabezados de la plantilla de RH (sin acentos) → campo interno */
    private const COLUMNAS = [
        'CODIGO'             => 'clave',
        'NOMBRE'             => 'nombre',
        'FECHA DE INGRESO'   => 'ingreso',
        'DEPARTAMENTO'       => 'departamento',
        'PUESTO'             => 'puesto',
        'UBICACION'          => 'ubicacion',
        'JEFE INMEDIATO'     => 'jefe',
        'HORARIO'            => 'horario',
        'DIA DE NACIMIENTO'  => 'dia',
        'MES DE NACIMIENTO'  => 'mes',
        'CORREO ELECTRONICO' => 'correo',
    ];

    /** Partes de apellidos compuestos: "DE LA CRUZ", "DEL VILLAR", "DE LOS SANTOS" */
    private const PARTICULAS = ['DE', 'DEL', 'LA', 'LAS', 'LOS', 'Y', 'SAN', 'VAN', 'MC'];

    /** Texto en UBICACIÓN → palabras para encontrar la sede en la intranet. Lo demás es Guadalajara. */
    private const SEDES = [
        'CDMX'   => ['CDMX', 'CIUDAD DE MEXICO', 'MEXICO'],
        'MTY'    => ['MONTERREY', 'MTY'],
        'QRO'    => ['QUERETARO', 'QRO'],
        'PUEBLA' => ['PUEBLA'],
        'TOLUCA' => ['TOLUCA'],
    ];

    /** Departamentos de RH que en la intranet pueden tener otro nombre */
    private const DEPARTAMENTOS_ALIAS = [
        'TALENTO Y CULTURA' => ['RECURSOS HUMANOS', 'RH'],
    ];

    private Collection $usuarios;
    private Collection $sedes;
    private Collection $departamentos;
    private array $reporte = [];
    private array $ligados = [];

    public function handle(): int
    {
        $filas = $this->leer($this->argument('archivo'));
        if ($filas === null) {
            return self::FAILURE;
        }

        $aplicar = (bool) $this->option('aplicar');
        $this->info(($aplicar ? 'IMPORTANDO' : 'SIMULANDO (no se guarda nada)') . ': ' . count($filas) . ' empleados en el archivo.');

        $this->usuarios = User::withoutGlobalScopes()->get();
        $this->sedes = Sede::all();
        $this->departamentos = Department::all();

        DB::beginTransaction();

        try {
            $coincidencias = $this->emparejar($filas);
            $nuevos = [];

            foreach ($filas as $i => $f) {
                [$usuario, $por] = $coincidencias[$i] ?? [null, null];
                $nuevo = $this->procesar($i, $f, $usuario, $por);
                if ($nuevo) {
                    $nuevos[] = [$nuevo, $f, $i];
                }
            }

            // Los jefes al final, porque algunos jefes también pueden ser altas nuevas de este archivo
            foreach ($nuevos as [$usuario, $f, $i]) {
                $this->asignarJefe($usuario, $f['jefe'], $i, true);
            }
            if ($this->option('actualizar-jefes')) {
                foreach ($filas as $i => $f) {
                    if (($coincidencias[$i][0] ?? null) instanceof User) {
                        $this->asignarJefe($coincidencias[$i][0], $f['jefe'], $i, false);
                    }
                }
            }

            $this->noEstanEnRh();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $aplicar ? DB::commit() : DB::rollBack();

        $this->resumen($aplicar);

        return self::SUCCESS;
    }

    // ─── Leer el archivo ─────────────────────────────────────────────────────

    /** Lee el CSV (con coma o punto y coma, UTF-8 o el de Excel en Windows) y busca la fila de encabezados. */
    private function leer(string $ruta): ?array
    {
        if (! is_readable($ruta)) {
            $this->error("No encuentro el archivo: {$ruta}");

            return null;
        }

        $contenido = file_get_contents($ruta);
        $contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido);
        if (! mb_check_encoding($contenido, 'UTF-8')) {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', 'Windows-1252');
        }

        $lineas = preg_split('/\r\n|\r|\n/', $contenido);
        $delimitador = substr_count($lineas[0] ?? '', ';') > substr_count($lineas[0] ?? '', ',') ? ';' : ',';
        $renglones = array_map(fn ($l) => str_getcsv($l, $delimitador), array_filter($lineas, fn ($l) => trim($l, " \t;,") !== ''));

        // La fila de encabezados es la que trae CODIGO y NOMBRE (RH a veces deja renglones arriba)
        $mapa = null;
        foreach ($renglones as $n => $r) {
            $normal = array_map(fn ($v) => self::normalizar((string) $v), $r);
            if (in_array('CODIGO', $normal, true) && in_array('NOMBRE', $normal, true)) {
                $mapa = [];
                foreach ($normal as $col => $titulo) {
                    if (isset(self::COLUMNAS[$titulo])) {
                        $mapa[self::COLUMNAS[$titulo]] = $col;
                    }
                }
                $renglones = array_slice($renglones, $n + 1);
                break;
            }
        }

        if (! $mapa) {
            $this->error('No encontré la fila de encabezados (con CODIGO y NOMBRE).');

            return null;
        }

        $faltan = array_diff(['clave', 'nombre', 'ingreso', 'dia', 'mes'], array_keys($mapa));
        if ($faltan) {
            $this->error('Faltan columnas en el archivo: ' . implode(', ', $faltan));

            return null;
        }

        $filas = [];
        foreach ($renglones as $r) {
            $f = [];
            foreach (array_unique(self::COLUMNAS) as $campo) {
                $f[$campo] = isset($mapa[$campo]) ? trim((string) ($r[$mapa[$campo]] ?? '')) : '';
            }
            if ($f['clave'] !== '' && $f['nombre'] !== '') {
                $filas[] = $f;
            }
        }

        return $filas;
    }

    // ─── Encontrar a quienes ya están en la intranet ─────────────────────────

    /**
     * Primero por clave de empleado (sin ceros a la izquierda). A quien no tenga clave,
     * por nombre, solo si la coincidencia es única en los dos sentidos.
     */
    private function emparejar(array $filas): array
    {
        $porClave = $this->usuarios->filter(fn ($u) => filled($u->clave_empleado))
            ->groupBy(fn ($u) => self::clave($u->clave_empleado));
        $clavesRh = collect($filas)->map(fn ($f) => self::clave($f['clave']))->flip();

        $resultado = [];

        foreach ($filas as $i => $f) {
            $candidatos = $porClave[self::clave($f['clave'])] ?? collect();
            if ($candidatos->isEmpty()) {
                continue;
            }

            // La clave sola no basta: también tiene que parecerse el nombre
            $mismos = $candidatos->filter(fn ($u) => self::seParecen($u->name, $f['nombre']));

            if ($mismos->count() === 1) {
                $resultado[$i] = [$mismos->first(), 'clave'];
                $this->ligados[$mismos->first()->id] = true;
            } else {
                // Nadie (o más de uno) con esa clave se llama igual: no se toca ni se registra
                $resultado[$i] = [null, 'conflicto'];
                $this->aviso($i, "En la intranet, la clave {$f['clave']} es de: " . $candidatos->pluck('name')->implode(', ')
                    . ($mismos->isEmpty() ? ', que no se llama(n) igual' : ' (hay más de uno con este nombre)')
                    . '. Corrige la clave en la intranet o en el archivo de RH.');
            }
        }

        // Por nombre: solo usuarios que no quedaron ligados y cuya clave no es de otro empleado del archivo
        $libres = $this->usuarios->reject(fn ($u) => isset($this->ligados[$u->id])
            || (filled($u->clave_empleado) && $clavesRh->has(self::clave($u->clave_empleado))));

        $opciones = [];
        foreach ($filas as $i => $f) {
            if (isset($resultado[$i])) {
                continue;
            }
            $tokensRh = self::tokens($f['nombre']);
            $opciones[$i] = $libres->filter(function ($u) use ($tokensRh) {
                $tokensU = self::tokens($u->name);

                return count($tokensU) >= 2 && ! array_diff($tokensU, $tokensRh);
            })->pluck('id')->all();
        }

        $vecesUsuario = array_count_values(array_merge([], ...array_values($opciones)));
        foreach ($opciones as $i => $ids) {
            if (count($ids) === 1 && $vecesUsuario[$ids[0]] === 1) {
                $usuario = $this->usuarios->firstWhere('id', $ids[0]);
                $resultado[$i] = [$usuario, 'nombre'];
                $this->ligados[$usuario->id] = true;
            } elseif ($ids) {
                // Se parece a alguien que también se parece a otra fila (o a varios): mejor no adivinar
                $resultado[$i] = [null, 'conflicto'];
                $this->aviso($i, 'Su nombre se parece a: ' . $this->usuarios->whereIn('id', $ids)->pluck('name')->implode(', ')
                    . '. No se registró para no duplicarlo: ponle su clave en la intranet y vuelve a importar.');
            }
        }

        return $resultado;
    }

    // ─── Actualizar o registrar ──────────────────────────────────────────────

    /** Regresa el usuario si se registró como nuevo (para asignarle jefe al final). */
    private function procesar(int $i, array $f, ?User $usuario, ?string $por): ?User
    {
        $this->reporte[$i] = [
            'clave'       => $f['clave'],
            'nombre_rh'   => $f['nombre'],
            'accion'      => '',
            'id'          => '',
            'nombre'      => '',
            'encontrado'  => $por ?? '',
            'cambios'     => '',
            'correo'      => '',
            'sede'        => '',
            'departamento' => '',
            'jefe'        => '',
            'turno'       => '',
            'avisos'      => $this->reporte[$i]['avisos'] ?? [],
        ];

        $ingreso = self::fecha($f['ingreso']);
        $nacimiento = self::nacimiento($f['dia'], $f['mes']);
        $horario = self::horario($f['horario']);
        $turno = self::turno($horario);

        if (! $ingreso) {
            $this->aviso($i, "No entendí la fecha de ingreso «{$f['ingreso']}»");
        }
        if (! $nacimiento) {
            $this->aviso($i, "No entendí el nacimiento «{$f['dia']}/{$f['mes']}»");
        }
        if ($f['horario'] !== '' && ! $horario) {
            $this->aviso($i, "No entendí el horario «{$f['horario']}»");
        }

        $datos = array_filter([
            'fecha_ingreso'    => $ingreso,
            'fecha_nacimiento' => $nacimiento,
            'puesto_empleado'  => $f['puesto'] !== '' ? self::titulo($f['puesto']) : null,
            // Del horario solo se toma el turno (diurno o nocturno); el horario no se guarda
            'turno'            => $turno,
        ], fn ($v) => $v !== null);

        $this->reporte[$i]['turno'] = $turno ?? '';

        if ($por === 'conflicto') {
            $this->reporte[$i]['accion'] = 'conflicto';

            return null;
        }

        try {
            if ($usuario) {
                // Ya existe: solo los datos de RH. Nombre, correo, sede, departamento y jefe se respetan.
                if (blank($usuario->clave_empleado)) {
                    $datos['clave_empleado'] = $f['clave'];
                }
                $usuario->forceFill($datos);
                $cambios = array_keys($usuario->getDirty());
                $usuario->save();

                $this->reporte[$i]['accion'] = $cambios ? 'actualizado' : 'sin cambios';
                $this->reporte[$i]['cambios'] = implode(', ', $cambios);
                if (! $usuario->activo) {
                    $this->aviso($i, 'Está INACTIVO en la intranet, pero RH lo tiene en la lista');
                }
                if ($por === 'nombre') {
                    $this->aviso($i, 'Se encontró por NOMBRE: confirma que es la misma persona');
                }
                $this->completar($i, $usuario);

                return null;
            }

            // No existe: se registra
            $sede = $this->sede($f['ubicacion'], $i);
            $departamento = $this->departamento($f['departamento']);

            if (! $sede || ! $departamento) {
                $this->reporte[$i]['accion'] = 'error';
                $this->aviso($i, ! $sede ? 'No encontré la sede en la intranet' : "No existe el departamento «{$f['departamento']}» (usa --crear-departamentos)");

                return null;
            }

            [$correo, $provisional] = $this->correoPara($f['correo'], $f['clave'], $i);

            $usuario = new User();
            $usuario->forceFill($datos + [
                'name'                  => self::nombreBonito($f['nombre']),
                'email'                 => $correo,
                'password'              => Hash::make(Str::random(40)),
                'clave_empleado'        => $f['clave'],
                'departamento_id'       => $departamento->id,
                'sede_id'               => $sede->id,
                'supervisor_id'         => $this->buscarJefe($f['jefe'])?->id,
                'activo'                => true,
                'incluir_celebraciones' => true,
            ])->save();

            if (Role::where('name', 'empleado')->exists()) {
                $usuario->assignRole('empleado');
            }

            $this->usuarios->push($usuario);
            $this->ligados[$usuario->id] = true;
            $this->reporte[$i]['accion'] = 'creado';
            if ($provisional) {
                $this->reporte[$i]['avisos'][] = 'Correo provisional';
            }
            $this->completar($i, $usuario);

            return $usuario;
        } catch (\Throwable $e) {
            $this->reporte[$i]['accion'] = 'error';
            $this->aviso($i, 'Error al guardar: ' . Str::limit($e->getMessage(), 200));

            return null;
        }
    }

    private function completar(int $i, User $u): void
    {
        $this->reporte[$i]['id'] = $u->id;
        $this->reporte[$i]['nombre'] = $u->name;
        $this->reporte[$i]['correo'] = $u->email;
        $this->reporte[$i]['sede'] = $this->sedes->firstWhere('id', $u->sede_id)?->nombre ?? '';
        $this->reporte[$i]['departamento'] = $this->departamentos->firstWhere('id', $u->departamento_id)?->name ?? '';
        $this->reporte[$i]['jefe'] = $this->usuarios->firstWhere('id', $u->supervisor_id)?->name ?? '';
    }

    private function asignarJefe(User $usuario, string $jefeRh, int $i, bool $esNuevo): void
    {
        if ($esNuevo && $usuario->supervisor_id) {
            return; // ya se encontró al registrarlo
        }

        $jefe = $this->buscarJefe($jefeRh);

        if ($jefe && $jefe->id !== $usuario->id && $usuario->supervisor_id !== $jefe->id) {
            $usuario->forceFill(['supervisor_id' => $jefe->id])->save();
            $this->reporte[$i]['jefe'] = $jefe->name;
            if (! $esNuevo) {
                $this->reporte[$i]['cambios'] = trim($this->reporte[$i]['cambios'] . ', supervisor_id', ', ');
            }
        } elseif (! $jefe && $esNuevo && $jefeRh !== '' && self::normalizar($jefeRh) !== 'VACANTE') {
            $this->aviso($i, "No encontré a su jefe «{$jefeRh}» en la intranet: queda sin jefe");
        }
    }

    /** "GRISELDA BALTIERRA" → el usuario cuyo nombre contiene esas palabras (si es solo uno). */
    private function buscarJefe(string $jefeRh): ?User
    {
        $tokens = self::tokens($jefeRh);
        if (count($tokens) < 2) {
            return null;
        }

        $candidatos = $this->usuarios->filter(fn ($u) => $u->activo && ! array_diff($tokens, self::tokens($u->name)));
        if ($candidatos->count() > 1) {
            $candidatos = $candidatos->filter(fn ($u) => method_exists($u, 'hasRole') && $u->hasRole('jefe'));
        }

        return $candidatos->count() === 1 ? $candidatos->first() : null;
    }

    private function sede(string $ubicacion, int $i): ?Sede
    {
        $texto = self::normalizar($ubicacion);
        $buscar = ['GUADALAJARA', 'GDL'];
        foreach (self::SEDES as $clave => $palabras) {
            if (str_contains($texto, $clave)) {
                $buscar = $palabras;
                break;
            }
        }

        $encontrar = function (array $palabras) {
            foreach ($palabras as $p) {
                $sede = $this->sedes->first(fn ($s) => str_contains(self::normalizar($s->nombre), $p));
                if ($sede) {
                    return $sede;
                }
            }

            return null;
        };

        $sede = $encontrar($buscar);
        if (! $sede && $buscar[0] !== 'GUADALAJARA') {
            $sede = $encontrar(['GUADALAJARA', 'GDL']);
            if ($sede) {
                $this->aviso($i, "No hay sede para «{$ubicacion}» en la intranet: se puso en {$sede->nombre}");
            }
        }

        return $sede;
    }

    private function departamento(string $nombre): ?Department
    {
        if ($nombre === '') {
            return null;
        }

        $buscar = [self::normalizar($nombre), ...(self::DEPARTAMENTOS_ALIAS[self::normalizar($nombre)] ?? [])];
        $encontrado = $this->departamentos->first(fn ($d) => in_array(self::normalizar($d->name), $buscar, true));

        if (! $encontrado && $this->option('crear-departamentos')) {
            $encontrado = Department::create(['name' => self::titulo($nombre)]);
            $this->departamentos->push($encontrado);
        }

        return $encontrado;
    }

    /** El correo de RH si es válido y no lo usa nadie; si no, uno provisional que nunca recibe correos. */
    private function correoPara(string $correo, string $clave, int $i): array
    {
        $correo = Str::lower(trim($correo));

        if ($correo !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)
            && ! User::withoutGlobalScopes()->where('email', $correo)->exists()) {
            return [$correo, false];
        }

        if ($correo !== '') {
            $this->aviso($i, "El correo «{$correo}» no es válido o ya lo usa otra persona");
        }

        return ['empleado.' . Str::slug($clave) . '@' . User::DOMINIO_SIN_CORREO, true];
    }

    // ─── Reportes ────────────────────────────────────────────────────────────

    private function noEstanEnRh(): void
    {
        foreach ($this->usuarios as $u) {
            if ($u->activo && ! isset($this->ligados[$u->id])) {
                $this->reporte[] = [
                    'clave' => (string) $u->clave_empleado, 'nombre_rh' => '', 'accion' => 'no está en la lista de RH',
                    'id' => $u->id, 'nombre' => $u->name, 'encontrado' => '', 'cambios' => '', 'correo' => $u->email,
                    'sede' => '', 'departamento' => '', 'jefe' => '', 'turno' => '',
                    'avisos' => ['Activo en la intranet, pero no viene en el archivo de RH (¿baja o usuario de prueba?)'],
                ];
            }
        }
    }

    private function aviso(int $i, string $texto): void
    {
        $this->reporte[$i]['avisos'][] = $texto;
    }

    private function resumen(bool $aplicar): void
    {
        $filas = collect($this->reporte);
        $cuenta = fn ($accion) => $filas->where('accion', $accion)->count();

        $this->newLine();
        $this->table(['', 'Empleados'], [
            ['Registrados nuevos', $cuenta('creado')],
            ['Actualizados', $cuenta('actualizado')],
            ['Ya estaban al día', $cuenta('sin cambios')],
            ['Con error (no se guardaron)', $cuenta('error')],
            ['Conflicto de clave o nombre (no se tocaron)', $cuenta('conflicto')],
            ['Encontrados por nombre (revisar)', $filas->where('encontrado', 'nombre')->count()],
            ['Con correo provisional', $filas->filter(fn ($f) => in_array('Correo provisional', $f['avisos'], true))->count()],
            ['Activos en la intranet que no vienen en RH', $cuenta('no está en la lista de RH')],
        ]);

        $errores = $filas->whereIn('accion', ['error', 'conflicto']);
        foreach ($errores->take(15) as $f) {
            $this->error("{$f['clave']} {$f['nombre_rh']}: " . implode(' · ', $f['avisos']));
        }

        // El reporte completo, para abrir en Excel
        $ruta = storage_path('app/importaciones/empleados-rh-' . now()->format('Ymd-His') . ($aplicar ? '' : '-simulacion') . '.csv');
        File::ensureDirectoryExists(dirname($ruta));
        $archivo = fopen($ruta, 'w');
        fwrite($archivo, "\xEF\xBB\xBF");
        fputcsv($archivo, ['Clave', 'Nombre en RH', 'Resultado', 'ID', 'Nombre en la intranet', 'Encontrado por', 'Campos cambiados', 'Correo', 'Sede', 'Departamento', 'Jefe', 'Turno', 'Avisos']);
        foreach ($filas as $f) {
            fputcsv($archivo, [...array_values(array_diff_key($f, ['avisos' => 1])), implode(' · ', $f['avisos'])]);
        }
        fclose($archivo);

        $this->newLine();
        $this->info("Reporte completo: {$ruta}");
        $this->line($aplicar
            ? 'Listo, los cambios se guardaron.'
            : 'Fue una SIMULACIÓN: no se guardó nada. Revisa el reporte y, si todo está bien, corre otra vez con --aplicar.');
    }

    // ─── Ayudas (públicas para poder probarlas) ──────────────────────────────

    /** "  José  Ñúñez " → "JOSE NUNEZ" */
    public static function normalizar(string $texto): string
    {
        $texto = strtr(mb_strtoupper($texto), ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N']);

        return trim(preg_replace('/\s+/', ' ', preg_replace('/[^A-Z0-9]/', ' ', $texto)));
    }

    /** Las palabras de un nombre, sin "de", "la", "del"... */
    public static function tokens(string $nombre): array
    {
        return array_values(array_diff(explode(' ', self::normalizar($nombre)), self::PARTICULAS, ['']));
    }

    /**
     * ¿Es la misma persona? "Omar Arellano" y "ARELLANO DELGADO OMAR GUADALUPE" sí.
     * Tienen que compartir al menos dos palabras (o todas, si en la intranet solo tiene una).
     */
    public static function seParecen(string $nombreIntranet, string $nombreRh): bool
    {
        $intranet = array_unique(self::tokens($nombreIntranet));
        $comunes = array_intersect($intranet, self::tokens($nombreRh));

        return count($intranet) > 0 && count($comunes) >= min(2, count($intranet));
    }

    /** "0005" y "5" son la misma clave */
    public static function clave(string $clave): string
    {
        $digitos = preg_replace('/\D/', '', $clave);

        return $digitos !== '' ? (ltrim($digitos, '0') ?: '0') : self::normalizar($clave);
    }

    /** "DE LA CRUZ BARAJAS AURORA ANAYELI" → "Aurora Anayeli de la Cruz Barajas" */
    public static function nombreBonito(string $nombreRh): string
    {
        $t = preg_split('/\s+/', trim(mb_strtoupper($nombreRh)));
        if (count($t) < 3) {
            return self::titulo(implode(' ', $t));
        }

        $i = 0;
        $paterno = self::apellido($t, $i);
        $materno = self::apellido($t, $i);
        $nombres = array_slice($t, $i);

        return self::titulo(implode(' ', $nombres ? [...$nombres, ...$paterno, ...$materno] : $t));
    }

    private static function apellido(array $t, int &$i): array
    {
        $partes = [];
        while ($i < count($t) && in_array(self::normalizar($t[$i]), self::PARTICULAS, true)) {
            $partes[] = $t[$i++];
        }
        if ($i < count($t)) {
            $partes[] = $t[$i++];
        }

        return $partes;
    }

    /** "MARIA DEL CARMEN" → "Maria del Carmen" */
    public static function titulo(string $texto): string
    {
        $palabras = explode(' ', mb_convert_case(mb_strtolower(trim(preg_replace('/\s+/', ' ', $texto))), MB_CASE_TITLE));
        foreach ($palabras as $n => $p) {
            if ($n > 0 && in_array($p, ['De', 'Del', 'La', 'Las', 'Los', 'Y'], true)) {
                $palabras[$n] = mb_strtolower($p);
            }
        }

        return implode(' ', $palabras);
    }

    /** "9:30-17:30" → "09:30-17:30" */
    public static function horario(string $texto): ?string
    {
        if (! preg_match('/^(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})$/', trim($texto), $m)) {
            return null;
        }

        return sprintf('%02d:%02d-%02d:%02d', $m[1], $m[2], $m[3], $m[4]);
    }

    /** Nocturno si entra a partir de las 19:00 o si la salida es al día siguiente. */
    public static function turno(?string $horario): ?string
    {
        if (! $horario) {
            return null;
        }
        [$entrada, $salida] = explode('-', $horario);

        return ($salida < $entrada || (int) substr($entrada, 0, 2) >= 19) ? 'nocturno' : 'diurno';
    }

    /** "06/09/2011", "2011-09-06" o el número de fecha de Excel */
    public static function fecha(string $texto): ?Carbon
    {
        $texto = trim($texto);
        if ($texto === '') {
            return null;
        }
        if (ctype_digit($texto) && (int) $texto > 20000 && (int) $texto < 80000) {
            return Carbon::create(1899, 12, 30)->addDays((int) $texto)->startOfDay();
        }

        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y', 'd/m/y', 'j/n/Y'] as $formato) {
            try {
                $fecha = Carbon::createFromFormat('!' . $formato, $texto);
                if ($fecha && $fecha->format($formato) === $texto && $fecha->year > 1950 && $fecha->lte(now()->addYear())) {
                    return $fecha->startOfDay();
                }
            } catch (\Throwable) {
                // probar el siguiente
            }
        }

        return null;
    }

    /**
     * RH solo da día y mes: se guarda con el año 2000 (bisiesto, para que quepa el 29 de febrero).
     * El sistema nunca muestra el año, así que la edad no existe en la intranet.
     */
    public static function nacimiento(string $dia, string $mes): ?Carbon
    {
        $d = (int) $dia;
        $m = (int) $mes;

        return checkdate($m, $d, 2000) ? Carbon::create(2000, $m, $d)->startOfDay() : null;
    }
}