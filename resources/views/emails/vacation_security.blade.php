<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Aviso Seguridad</title></head>
<body style="margin:0; padding:0; background:#f4eff7; font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
    <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">
            <tr><td style="background:linear-gradient(135deg,#473524 0%,#6A2C75 100%); border-radius:16px 16px 0 0; padding:28px 36px; text-align:center;">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; display:block; margin:0 auto 12px;">
                <p style="margin:0; font-size:13px; color:rgba(255,255,255,.75); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Aviso de Seguridad</p>
            </td></tr>
            <tr><td style="background:#fff; padding:28px 36px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf3e0; border:1.5px solid #EED39B; border-radius:10px; padding:14px 16px; margin-bottom:20px;">
                    <tr>
                        <td style="font-size:24px; padding-right:12px; vertical-align:middle;">🔐</td>
                        <td><p style="margin:0; font-size:14px; font-weight:800; color:#473524;">Aviso para el área de Seguridad</p>
                            <p style="margin:3px 0 0; font-size:12px; color:#7a6050;">El siguiente empleado tiene vacaciones aprobadas. Permítele la salida en las fechas indicadas.</p>
                        </td>
                    </tr>
                </table>
                <h1 style="margin:0 0 4px; font-size:20px; font-weight:800; color:#2c1a30;">Vacaciones Aprobadas</h1>
            </td></tr>
            <tr><td style="background:#fff; padding:20px 36px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3eef5; border-radius:10px; border:1px solid #e6d9ed; padding:16px;">
                    <tr>
                        <td style="width:40px; vertical-align:middle; text-align:center;">
                            <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#6A2C75,#BBA4C0); color:#fff; font-size:16px; font-weight:800; text-align:center; line-height:40px; display:inline-block;">
                                {{ strtoupper(substr($vacationRequest->empleado->name, 0, 1)) }}
                            </div>
                        </td>
                        <td style="padding-left:12px;">
                            <p style="margin:0; font-size:15px; font-weight:700; color:#2c1a30;">{{ $vacationRequest->empleado->name }}</p>
                            <p style="margin:2px 0 0; font-size:12px; color:#7a6682;">{{ $vacationRequest->empleado->puesto_empleado ?? '' }}</p>
                        </td>
                    </tr>
                </table>
            </td></tr>
            <tr><td style="background:#fff; padding:16px 36px 0;">
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr><td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">📅 Período</td>
                            <td style="font-size:13px; font-weight:700; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($vacationRequest->fecha_fin)->format('d/m/Y') }}</td>
                        </tr></table>
                    </td></tr>
                    <tr><td style="padding:8px 0;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">🔄 Regresa el</td>
                            <td style="font-size:13px; font-weight:700; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_reincorporacion)->format('d/m/Y') }}</td>
                        </tr></table>
                    </td></tr>
                </table>
            </td></tr>
            <tr><td style="background:#fff; padding:20px 36px;">
                <table width="100%"><tr><td align="center">
                    <a href="{{ route('vacaciones.show', $vacationRequest->id) }}" style="display:inline-block; background:#6A2C75; color:#fff; text-decoration:none; font-size:14px; font-weight:700; padding:12px 28px; border-radius:9px;">Ver Detalles</a>
                </td></tr></table>
            </td></tr>
            <tr><td style="background:#f7f3ef; border-top:1.5px solid #e6d9ed; border-radius:0 0 16px 16px; padding:18px 36px; text-align:center;">
                <p style="margin:0 0 4px; font-size:12px; color:#7a6682;">Generado automáticamente por <strong style="color:#6A2C75;">{{ config('app.name') }}</strong></p>
                <p style="margin:0; font-size:11px; color:#BBA4C0;">Por favor no respondas a este correo.</p>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>