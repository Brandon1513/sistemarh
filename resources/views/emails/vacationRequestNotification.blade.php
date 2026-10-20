<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Vacaciones</title>
</head>
<body style="margin:0; padding:0; background:#f4eff7; font-family: 'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                    {{-- ── Header ── --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#6A2C75 0%,#9b4dab 100%); border-radius:16px 16px 0 0; padding:28px 36px; text-align:center;">
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; margin-bottom:10px; display:block; margin-left:auto; margin-right:auto;">
                            <p style="margin:0; font-size:13px; color:rgba(255,255,255,.7); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Sistema de Recursos Humanos</p>
                        </td>
                    </tr>

                    {{-- ── Título solicitud ── --}}
                    <tr>
                        <td style="background:#fff; padding:28px 36px 0;">

                            @if(isset($vacationRequest->tipo) && $vacationRequest->tipo === 'espontanea')
                            {{-- Alerta espontánea --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                                <tr>
                                    <td style="background:#faf3e0; border:1.5px solid #EED39B; border-radius:10px; padding:12px 16px;">
                                        <table cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="font-size:20px; padding-right:10px; vertical-align:middle;">📋</td>
                                                <td>
                                                    <p style="margin:0; font-size:13px; font-weight:700; color:#473524;">Vacación Registrada por Recursos Humanos</p>
                                                    <p style="margin:4px 0 0; font-size:12px; color:#7a6050;">Esta solicitud fue creada por el área de RH en nombre del empleado, correspondiente a días de vacaciones ya disfrutados.</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="margin:0 0 6px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#BBA4C0;">Nueva Solicitud</p>
                            <h1 style="margin:0 0 4px; font-size:22px; font-weight:800; color:#2c1a30;">Solicitud de Vacaciones</h1>
                            <p style="margin:0; font-size:14px; color:#7a6682;">
                                Hola <strong style="color:#2c1a30;">{{ $vacationRequest->empleado->supervisor->name ?? 'Supervisor' }}</strong>, tienes una nueva solicitud pendiente de revisión.
                            </p>
                        </td>
                    </tr>

                    {{-- ── Card empleado ── --}}
                    <tr>
                        <td style="background:#fff; padding:20px 36px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3eef5; border-radius:10px; padding:16px; border:1px solid #e6d9ed;">
                                <tr>
                                    <td style="width:44px; vertical-align:middle;">
                                        <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#6A2C75,#BBA4C0); display:inline-flex; align-items:center; justify-content:center; color:#fff; font-size:16px; font-weight:800; text-align:center; line-height:40px;">
                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                        </div>
                                    </td>
                                    <td style="padding-left:12px; vertical-align:middle;">
                                        <p style="margin:0; font-size:14px; font-weight:700; color:#2c1a30;">{{ $employee->name }}</p>
                                        <p style="margin:2px 0 0; font-size:12px; color:#7a6682;">{{ $employee->puesto_empleado ?? '' }} · {{ $employee->departamento->name ?? '' }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ── Detalles ── --}}
                    <tr>
                        <td style="background:#fff; padding:20px 36px 0;">
                            <p style="margin:0 0 12px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#6A2C75;">Detalles de la Solicitud</p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682; width:50%;">📅 Fecha de Inicio</td>
                                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_inicio)->format('d/m/Y') }}</td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682; width:50%;">📅 Fecha de Término</td>
                                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_fin)->format('d/m/Y') }}</td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682; width:50%;">🔄 Reincorporación</td>
                                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_reincorporacion)->format('d/m/Y') }}</td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682; width:50%;">📆 Período</td>
                                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ $vacationRequest->periodo_correspondiente }}</td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682; width:50%;">🌴 Días Solicitados</td>
                                            <td style="text-align:right;">
                                                <span style="background:#f3eef5; color:#6A2C75; font-size:13px; font-weight:800; padding:2px 10px; border-radius:20px;">{{ $vacationRequest->dias_solicitados }} días</span>
                                            </td>
                                        </tr></table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ── Botones ── --}}
                    <tr>
                        <td style="background:#fff; padding:24px 36px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding-bottom:10px;">
                                        <a href="{{ route('vacaciones.show', $vacationRequest->id) }}"
                                           style="display:inline-block; background:#6A2C75; color:#fff; text-decoration:none; font-size:14px; font-weight:700; padding:12px 28px; border-radius:9px; letter-spacing:.03em;">
                                            Ver Solicitud Completa
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="padding-right:6px;">
                                                    <a href="{{ route('vacaciones.aprobar', $vacationRequest->id) }}"
                                                       style="display:inline-block; background:#2d7a4f; color:#fff; text-decoration:none; font-size:13px; font-weight:700; padding:10px 24px; border-radius:9px;">
                                                        ✓ Aprobar
                                                    </a>
                                                </td>
                                                <td align="center" style="padding-left:6px;">
                                                    <a href="{{ route('vacaciones.rechazar', $vacationRequest->id) }}"
                                                       style="display:inline-block; background:#AA4969; color:#fff; text-decoration:none; font-size:13px; font-weight:700; padding:10px 24px; border-radius:9px;">
                                                        ✕ Rechazar
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#f7f3ef; border-top:1.5px solid #e6d9ed; border-radius:0 0 16px 16px; padding:18px 36px; text-align:center;">
                            <p style="margin:0 0 4px; font-size:12px; color:#7a6682;">Este correo fue generado automáticamente por <strong style="color:#6A2C75;">{{ config('app.name') }}</strong></p>
                            <p style="margin:0; font-size:11px; color:#BBA4C0;">Por favor no respondas a este correo.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>