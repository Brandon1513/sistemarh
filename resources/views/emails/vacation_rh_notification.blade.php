<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Actualización Vacaciones RH</title></head>
<body style="margin:0; padding:0; background:#f4eff7; font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
    <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

            {{-- Header --}}
            <tr><td style="background:linear-gradient(135deg,#6A2C75 0%,#9b4dab 100%); border-radius:16px 16px 0 0; padding:28px 36px; text-align:center;">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; display:block; margin:0 auto 12px;">
                <p style="margin:0; font-size:13px; color:rgba(255,255,255,.75); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Sistema de Recursos Humanos</p>
            </td></tr>

            {{-- Contenido --}}
            <tr><td style="background:#fff; padding:28px 36px 0;">
                <p style="margin:0 0 6px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#BBA4C0;">Actualización de Solicitud</p>
                <h1 style="margin:0 0 8px; font-size:22px; font-weight:800; color:#2c1a30;">Vacaciones {{ ucfirst($status) }}</h1>
                <p style="margin:0; font-size:14px; color:#7a6682; line-height:1.6;">Hola <strong style="color:#2c1a30;">Recursos Humanos</strong>, te informamos que la siguiente solicitud ha sido actualizada.</p>
            </td></tr>

            {{-- Badge estado --}}
            <tr><td style="background:#fff; padding:16px 36px 0;">
                @if($status === 'aprobada')
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#e8f5ee; border:1.5px solid #b3dfc3; border-radius:10px; padding:12px 16px;">
                    <tr>
                        <td style="font-size:20px; padding-right:10px; vertical-align:middle;">✅</td>
                        <td><p style="margin:0; font-size:14px; font-weight:700; color:#1b6b38;">Solicitud Aprobada por el Jefe Directo</p></td>
                    </tr>
                </table>
                @else
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#fce8ee; border:1.5px solid #f0b3c3; border-radius:10px; padding:12px 16px;">
                    <tr>
                        <td style="font-size:20px; padding-right:10px; vertical-align:middle;">❌</td>
                        <td><p style="margin:0; font-size:14px; font-weight:700; color:#7a2039;">Solicitud Rechazada por el Jefe Directo</p></td>
                    </tr>
                </table>
                @endif
            </td></tr>

            {{-- Card empleado --}}
            <tr><td style="background:#fff; padding:16px 36px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3eef5; border-radius:10px; border:1px solid #e6d9ed; padding:14px 16px;">
                    <tr>
                        <td style="width:40px; vertical-align:middle; text-align:center;">
                            <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#6A2C75,#BBA4C0); color:#fff; font-size:14px; font-weight:800; text-align:center; line-height:36px; display:inline-block;">
                                {{ strtoupper(substr($vacationRequest->empleado->name, 0, 1)) }}
                            </div>
                        </td>
                        <td style="padding-left:12px;">
                            <p style="margin:0; font-size:14px; font-weight:700; color:#2c1a30;">{{ $vacationRequest->empleado->name }}</p>
                            <p style="margin:2px 0 0; font-size:12px; color:#7a6682;">{{ $vacationRequest->empleado->puesto_empleado ?? '' }} · {{ $vacationRequest->departamento->name ?? '' }}</p>
                        </td>
                    </tr>
                </table>
            </td></tr>

            {{-- Detalles --}}
            <tr><td style="background:#fff; padding:16px 36px 0;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr><td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">📅 Fecha de Inicio</td>
                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_inicio)->format('d/m/Y') }}</td>
                        </tr></table>
                    </td></tr>
                    <tr><td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">📅 Fecha de Fin</td>
                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_fin)->format('d/m/Y') }}</td>
                        </tr></table>
                    </td></tr>
                    <tr><td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">🔄 Reincorporación</td>
                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_reincorporacion)->format('d/m/Y') }}</td>
                        </tr></table>
                    </td></tr>
                    <tr><td style="padding:8px 0;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">🌴 Días</td>
                            <td style="text-align:right;"><span style="background:#f3eef5; color:#6A2C75; font-size:13px; font-weight:800; padding:2px 10px; border-radius:20px;">{{ $vacationRequest->dias_solicitados }} días</span></td>
                        </tr></table>
                    </td></tr>
                </table>
            </td></tr>

            {{-- Footer --}}
            <tr><td style="background:#f7f3ef; border-top:1.5px solid #e6d9ed; border-radius:0 0 16px 16px; padding:18px 36px; margin-top:24px; text-align:center;">
                <p style="margin:0 0 4px; font-size:12px; color:#7a6682;">Este correo fue generado automáticamente por <strong style="color:#6A2C75;">{{ config('app.name') }}</strong></p>
                <p style="margin:0; font-size:11px; color:#BBA4C0;">Por favor no respondas a este correo.</p>
            </td></tr>

        </table>
    </td></tr>
</table>
</body>
</html>