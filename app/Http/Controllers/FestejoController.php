<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use App\Services\Celebraciones\CelebracionesService;
use App\Services\Celebraciones\GeneradorTarjetas;
use App\Services\ComunicadosService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * La invitación al festejo de cumpleaños del mes. La crea RH; la imagen la arma el servidor.
 */
class FestejoController extends Controller
{
    public function __construct(
        private GeneradorTarjetas $generador,
        private CelebracionesService $celebraciones,
        private ComunicadosService $comunicados,
    ) {
    }

    public function create()
    {
        return view('celebraciones.festejo', [
            'valores' => [
                'variante' => 'general',
                'turno'    => 'diurno',
                'mes'      => now()->format('Y-m'),
                'fecha'    => null,
                'hora'     => '13:30',
                'lugar'    => 'el comedor',
                'nota'     => 'Puedes llevar a un invitado de tu propio equipo.',
            ],
        ]);
    }

    /** La imagen tal como va a quedar, mientras RH escribe. */
    public function vistaPrevia(Request $request)
    {
        $datos = $request->validate($this->reglas(false));
        [$variables, $secciones] = $this->preparar($datos);

        return response($this->generador->festejo($datos['variante'], $variables, $secciones))
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store');
    }

    public function store(Request $request)
    {
        $datos = $request->validate($this->reglas(true), [
            'fecha.after_or_equal'    => 'La fecha del festejo no puede ser en el pasado.',
            'publicar_en.required_if' => 'Elige la fecha y hora en que se va a publicar.',
            'publicar_en.after'       => 'La fecha de publicación tiene que ser en el futuro.',
        ]);

        [$variables, $secciones] = $this->preparar($datos);
        $mes = Carbon::parse($datos['mes'] . '-01');
        $nombreMes = $mes->copy()->locale('es')->isoFormat('MMMM');
        $esTurno = $datos['variante'] === 'turno';
        $nombreTurno = $esTurno ? ($datos['turno'] === 'nocturno' ? 'turno nocturno' : 'turno diurno') : null;

        $comunicado = Comunicado::create([
            'tipo'              => 'cumpleanos',
            'celebracion'       => 'festejo',
            'celebracion_fecha' => $datos['fecha'],
            'celebracion_datos' => $datos,
            'autor_id'          => $request->user()->id,
            'titulo'            => "🎉 Festejo de cumpleaños de {$nombreMes}" . ($esTurno ? " · " . ucfirst($nombreTurno) : ''),
            'resumen'           => $variables['HORA_LUGAR'] . ', el ' . mb_strtolower($variables['FECHA_LARGA']) . '.',
            'contenido'         => $this->texto($datos, $variables, $secciones, $nombreMes),
            'imagen'            => $this->generador->guardarFestejo($datos['variante'], $variables, $secciones, $mes),
            'firma'             => 'Recursos Humanos',
            'estado'            => 'borrador',
            'enviar_push'       => true,
            'enviar_correo'     => 'automatico',
            'usar_plantilla'    => false,
            'publicar_en'       => ($datos['cuando'] ?? 'ahora') === 'programar' ? Carbon::parse($datos['publicar_en']) : null,
        ]);

        // Igual que cualquier comunicado: ahora (con push y correo) o a la hora programada
        $this->comunicados->publicarOProgramar($comunicado);
        $comunicado->refresh();

        return redirect()->route('comunicados.admin.index')->with('success', $comunicado->estado === 'programado'
            ? "La invitación se publicará el " . $comunicado->publicar_en->locale('es')->isoFormat('D [de] MMMM [a las] H:mm') . '.'
            : 'La invitación ya está publicada; le llegará a todos por la app y por correo.');
    }

    // ─── Ayudas ──────────────────────────────────────────────────────────────

    private function reglas(bool $alGuardar): array
    {
        return [
            'variante'    => ['required', Rule::in(['general', 'turno'])],
            'turno'       => ['required_if:variante,turno', 'nullable', Rule::in(['diurno', 'nocturno'])],
            'mes'         => 'required|date_format:Y-m',
            'fecha'       => $alGuardar ? 'required|date|after_or_equal:today' : 'nullable|date',
            'hora'        => 'required|date_format:H:i',
            'lugar'       => 'required|string|max:60',
            'nota'        => 'nullable|string|max:120',
            'cuando'      => 'nullable|in:ahora,programar',
            'publicar_en' => ['required_if:cuando,programar', 'nullable', 'date', ...($alGuardar ? ['after:now'] : [])],
        ];
    }

    /** Los textos de la plantilla y, en la versión por turno, su lista. */
    private function preparar(array $d): array
    {
        $mes = Carbon::parse($d['mes'] . '-01');
        $fecha = ! empty($d['fecha']) ? Carbon::parse($d['fecha'])->locale('es') : null;
        $hora = Carbon::createFromFormat('H:i', $d['hora']);

        // "a la 1:30" / "a las 9:30"
        $articulo = (int) $hora->format('g') === 1 ? 'a la' : 'a las';

        $variables = [
            'MES'         => mb_strtoupper($mes->copy()->locale('es')->isoFormat('MMMM')),
            'DIA'         => $fecha ? mb_strtoupper($fecha->isoFormat('dddd')) : 'DÍA',
            'FECHA'       => $fecha ? mb_strtoupper($fecha->isoFormat('D [de] MMMM')) : 'FECHA',
            'FECHA_LARGA' => $fecha ? ucfirst($fecha->isoFormat('dddd')) . ' ' . $fecha->day . ' de ' . ucfirst($fecha->isoFormat('MMMM')) : 'Día y fecha',
            'HORA_LUGAR'  => "Te esperamos {$articulo} {$hora->format('h:i')} {$hora->format('a')} en " . trim($d['lugar']),
            'NOTA'        => trim((string) ($d['nota'] ?? '')),
        ];

        $secciones = $d['variante'] === 'turno'
            ? $this->celebraciones->seccionesDelMes($mes, $d['turno'])
            : [];

        return [$variables, $secciones];
    }

    /** El texto del comunicado (para quien no ve la imagen). */
    private function texto(array $d, array $v, array $secciones, string $nombreMes): string
    {
        $texto = $d['variante'] === 'turno'
            ? "Queremos festejar sus cumpleaños el próximo **{$v['FECHA_LARGA']}**.\n\n**{$v['HORA_LUGAR']}.**"
            : "Te invitamos a festejar con los cumpleañeros de {$nombreMes}.\n\n**" . ucfirst(mb_strtolower($v['DIA'])) . " " . mb_strtolower($v['FECHA']) . "**\n\n**{$v['HORA_LUGAR']}.**";

        if ($v['NOTA']) {
            $texto .= "\n\n{$v['NOTA']}";
        }

        foreach ($secciones as $titulo => $filas) {
            $texto .= "\n\n**" . mb_convert_case($titulo, MB_CASE_TITLE) . "**\n"
                . collect($filas)->map(fn ($f) => "{$f['dia']} · " . mb_convert_case($f['nombre'], MB_CASE_TITLE))->implode("\n");
        }

        return $texto;
    }
}