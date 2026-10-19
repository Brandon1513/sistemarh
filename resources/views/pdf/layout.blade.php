{{--
    Plantilla base para los formatos PDF de Recursos Humanos (DomPDF).

    Parámetros:
      $docTitle     Nombre del formato
      $docCode      Código del formato (control documental), opcional
      $docRevision  Revisión del formato, opcional
      $docIssued    Fecha de emisión del formato, opcional
      $folio        Folio de la solicitud
      $status       pendiente | aprobado | rechazado
      $requestedAt  Fecha en que se registró la solicitud
--}}
@php
    $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo-pdf.png')));

    $statusKey = strtolower((string) ($status ?? 'pendiente'));
    $statusLabel = match ($statusKey) {
        'aprobado', 'aprobada' => 'Aprobado',
        'rechazado', 'rechazada' => 'Rechazado',
        default => 'Pendiente',
    };
    $statusClass = match ($statusLabel) {
        'Aprobado' => 'status-approved',
        'Rechazado' => 'status-rejected',
        default => 'status-pending',
    };

    $generatedAt = now()->timezone('America/Mexico_City')->locale('es');

    // Si la fecha de registro trae hora se convierte a hora local; si es solo fecha se muestra tal cual
    $requestedLabel = '—';
    if (! empty($requestedAt)) {
        $req = \Carbon\Carbon::parse($requestedAt);
        $requestedLabel = $req->format('H:i:s') === '00:00:00'
            ? $req->locale('es')->translatedFormat('d \d\e F \d\e Y')
            : $req->timezone('America/Mexico_City')->locale('es')->translatedFormat('d \d\e F \d\e Y, H:i') . ' h';
    }
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $docTitle }} · {{ $folio }}</title>
    <style>
        @page { margin: 28px 34px 58px 34px; }

        * { box-sizing: border-box; }

        body {
            /* Helvetica es fuente base de PDF: no se incrusta y mantiene los archivos ligeros */
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.45;
            color: #2c1a30;
            margin: 0;
        }

        table { width: 100%; border-collapse: collapse; }

        /* ── Encabezado ───────────────────────────────── */
        .doc-header td { vertical-align: middle; }
        .doc-header .logo-cell { width: 78px; padding-right: 12px; }
        .doc-header .logo-cell img { width: 72px; }
        .brand-name {
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #D6A644;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 17px;
            font-weight: bold;
            color: #6A2C75;
            margin: 2px 0 1px;
        }
        .doc-subtitle { font-size: 8.5px; color: #7a6682; }

        .doc-control {
            width: 190px;
            border: 1px solid #e6d9ed;
            border-radius: 6px;
        }
        .doc-control td {
            padding: 4px 8px;
            font-size: 8px;
            border-bottom: 1px solid #e6d9ed;
        }
        .doc-control tr:last-child td { border-bottom: none; }
        .doc-control .k { color: #7a6682; text-transform: uppercase; letter-spacing: .5px; width: 72px; }
        .doc-control .v { font-weight: bold; color: #2c1a30; text-align: right; }
        .doc-control .folio { color: #6A2C75; font-size: 9.5px; }

        .rule { height: 3px; background: #6A2C75; margin: 12px 0 0; }
        .rule-gold { height: 2px; background: #D6A644; margin: 0 0 14px; }

        /* ── Banda de estado ──────────────────────────── */
        .status-band { background: #f7f3ef; border-radius: 6px; margin-bottom: 14px; }
        .status-band td { padding: 8px 12px; font-size: 8.5px; color: #7a6682; }
        .status-band strong { color: #2c1a30; }
        .status-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 10px;
            font-size: 8.5px;
            font-weight: bold;
            letter-spacing: .5px;
            text-transform: uppercase;
            vertical-align: middle;
        }
        .status-approved { background: #e3f2e8; color: #1f6b3f; border: 1px solid #a9d6b8; }
        .status-rejected { background: #fbe9e9; color: #a52a2a; border: 1px solid #efb4b4; }
        .status-pending  { background: #fdf3d7; color: #8a6100; border: 1px solid #EED39B; }

        /* ── Secciones ────────────────────────────────── */
        .section { margin-bottom: 12px; }
        .section-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #6A2C75;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 0 4px 8px;
            border-left: 3px solid #D6A644;
            margin-bottom: 6px;
        }

        .fields { border: 1px solid #e6d9ed; border-radius: 6px; }
        .fields td {
            padding: 6px 10px;
            border-bottom: 1px solid #efe6f2;
            vertical-align: top;
        }
        .fields tr:last-child td { border-bottom: none; }
        .fields .label {
            width: 22%;
            font-size: 7.5px;
            color: #7a6682;
            text-transform: uppercase;
            letter-spacing: .4px;
            background: #faf7fb;
        }
        .fields .value { width: 28%; font-weight: bold; }
        .fields .value.full { width: 78%; font-weight: normal; }

        /* ── Opciones (casillas) ──────────────────────── */
        .options td { padding: 6px 10px; border: 1px solid #e6d9ed; text-align: center; font-size: 8.5px; }
        .options .on { background: #f3eef5; color: #6A2C75; font-weight: bold; }
        .box {
            display: inline-block;
            width: 9px; height: 9px;
            border: 1.2px solid #6A2C75;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: -1px;
        }
        .box.checked { background: #6A2C75; }

        /* ── Indicadores numéricos ───────────────────── */
        .stats td { padding: 0 4px; }
        .stat {
            border: 1px solid #e6d9ed;
            border-radius: 6px;
            padding: 8px 6px;
            text-align: center;
        }
        .stat .num { font-size: 16px; font-weight: bold; color: #6A2C75; line-height: 1.1; }
        .stat .cap { font-size: 7px; color: #7a6682; text-transform: uppercase; letter-spacing: .4px; }
        .stat.gold { background: #faf3e0; border-color: #EED39B; }
        .stat.gold .num { color: #8a6100; }

        .note {
            border: 1px solid #e6d9ed;
            border-radius: 6px;
            padding: 8px 10px;
            min-height: 34px;
        }
        .muted { color: #7a6682; }

        /* ── Firmas ───────────────────────────────────── */
        .signatures { margin-top: 34px; }
        .signatures td { width: 33.33%; padding: 0 12px; text-align: center; vertical-align: bottom; }
        .sign-line { border-top: 1px solid #2c1a30; padding-top: 5px; }
        .sign-name { font-weight: bold; font-size: 8.5px; min-height: 12px; }
        .sign-role { font-size: 7.5px; color: #7a6682; text-transform: uppercase; letter-spacing: .4px; }
        .sign-status { font-size: 7.5px; margin-bottom: 4px; }

        /* ── Pie de página ────────────────────────────── */
        .footer {
            position: fixed;
            bottom: -40px;
            left: 0; right: 0;
            border-top: 1px solid #e6d9ed;
            padding-top: 6px;
            font-size: 7px;
            color: #7a6682;
        }
        .footer td { vertical-align: top; }
    </style>
</head>
<body>

    <div class="footer">
        <table>
            <tr>
                <td>
                    <strong style="color:#6A2C75;">DASAVENA</strong> · Recursos Humanos ·
                    Documento generado desde la Intranet el {{ $generatedAt->translatedFormat('d/m/Y \a \l\a\s H:i') }} h
                </td>
                <td style="text-align:right;">
                    {{ $docCode ?? 'Formato interno' }}@isset($docRevision) · Rev. {{ $docRevision }}@endisset · Folio {{ $folio }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Encabezado --}}
    <table class="doc-header">
        <tr>
            <td class="logo-cell"><img src="{{ $logo }}" alt="Dasavena"></td>
            <td>
                <div class="brand-name">Dasavena · Recursos Humanos</div>
                <div class="doc-title">{{ $docTitle }}</div>
                <div class="doc-subtitle">{{ $docSubtitle ?? '' }}</div>
            </td>
            <td style="width:190px;">
                <table class="doc-control">
                    @isset($docCode)
                        <tr><td class="k">Código</td><td class="v">{{ $docCode }}</td></tr>
                    @endisset
                    @isset($docRevision)
                        <tr><td class="k">Revisión</td><td class="v">{{ $docRevision }}</td></tr>
                    @endisset
                    @isset($docIssued)
                        <tr><td class="k">Emisión</td><td class="v">{{ $docIssued }}</td></tr>
                    @endisset
                    <tr><td class="k">Folio</td><td class="v folio">{{ $folio }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
    <div class="rule"></div>
    <div class="rule-gold"></div>

    {{-- Estado de la solicitud --}}
    <table class="status-band">
        <tr>
            <td>
                Solicitud registrada el
                <strong>{{ $requestedLabel }}</strong>
            </td>
            <td style="text-align:right;">
                Estado: <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
            </td>
        </tr>
    </table>

    @yield('content')

</body>
</html>
