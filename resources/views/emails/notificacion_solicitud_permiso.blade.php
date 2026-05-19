<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Permiso</title>
</head>
<body style="margin:0; padding:0; background:#f4eff7; font-family: 'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#6A2C75 0%,#9b4dab 100%); border-radius:16px 16px 0 0; padding:28px 36px; text-align:center;">
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; display:block; margin:0 auto 10px;">
                            <p style="margin:0; font-size:13px; color:rgba(255,255,255,.7); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Sistema de Recursos Humanos</p>
                        </td>
                    </tr>

                    {{-- Título --}}
                    <tr>
                        <td style="background:#fff; padding:28px 36px 0;">
                            <p style="margin:0 0 6px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#BBA4C0;">Nueva Solicitud</p>
                            <h1 style="margin:0 0 4px; font-size:22px; font-weight:800; color:#2c1a30;">Control de Ausencias</h1>
                            <p style="margin:0; font-size:14px; color:#7a6682;">
                                Hola <strong style="color:#2c1a30;">{{ $permiso->empleado->supervisor->name ?? 'Supervisor' }}</strong>, tienes una nueva solicitud pendiente de revisión.
                            </p>
                        </td>
                    </tr>

                    {{-- Card empleado --}}
                    <tr>
                        <td style="background:#fff; padding:20px 36px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3eef5; border-radius:10px; padding:16px; border:1px solid #e6d9ed;">
                                <tr>
                                    <td style="width:44px; vertical-align:middle; text-align:center;">
                                        <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#6A2C75,#BBA4C0); color:#fff; font-size:16px; font-weight:800; text-align:center; line-height:40px; display:inline-block;">
                                            {{ strtoupper(substr($permiso->empleado->name, 0, 1)) }}
                                        </div>
                                    </td>
                                    <td style="padding-left:12px; vertical-align:middle;">
                                        <p style="margin:0; font-size:14px; font-weight:700; color:#2c1a30;">{{ $permiso->empleado->name }}</p>
                                        <p style="margin:2px 0 0; font-size:12px; color:#7a6682;">{{ $permiso->departamento->name ?? '' }}</p>
                                    </td>
                                    <td style="text-align:right; vertical-align:middle;">
                                        <span style="background:#fff; border:1px solid #e6d9ed; color:#6A2C75; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;">
                                            {{ ucfirst($permiso->tipo_permiso ?? 'Permiso') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Detalles --}}
                    <tr>
                        <td style="background:#fff; padding:20px 36px 0;">
                            <p style="margin:0 0 12px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#6A2C75;">Detalles de la Solicitud</p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682;">📅 Fecha de Inicio</td>
                                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($permiso->fecha_inicio)->format('d/m/Y') }}</td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682;">📅 Fecha de Término</td>
                                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($permiso->fecha_termino)->format('d/m/Y') }}</td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682; vertical-align:top;">📝 Motivo</td>
                                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ $permiso->motivo }}</td>
                                        </tr></table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Botón --}}
                    <tr>
                        <td style="background:#fff; padding:24px 36px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ route('permisos.show', $permiso->id) }}"
                                           style="display:inline-block; background:#6A2C75; color:#fff; text-decoration:none; font-size:14px; font-weight:700; padding:12px 28px; border-radius:9px;">
                                            Revisar Solicitud
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
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