<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Vacaciones Aprobadas</title></head>
<body style="margin:0; padding:0; background:#f4eff7; font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
    <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

            {{-- Header --}}
            <tr><td style="background:linear-gradient(135deg,#2d7a4f 0%,#3da066 100%); border-radius:16px 16px 0 0; padding:28px 36px; text-align:center;">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; display:block; margin:0 auto 12px;">
                <p style="margin:0; font-size:13px; color:rgba(255,255,255,.75); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Sistema de Recursos Humanos</p>
            </td></tr>

            {{-- Badge aprobado --}}
            <tr><td style="background:#fff; padding:28px 36px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#e8f5ee; border:1.5px solid #b3dfc3; border-radius:10px; padding:14px 16px; margin-bottom:20px;">
                    <tr>
                        <td style="font-size:24px; padding-right:12px; vertical-align:middle;">✅</td>
                        <td><p style="margin:0; font-size:15px; font-weight:800; color:#1b6b38;">¡Solicitud Aprobada!</p>
                            <p style="margin:3px 0 0; font-size:12px; color:#2d7a4f;">Tu solicitud de vacaciones ha sido aprobada por tu jefe directo.</p>
                        </td>
                    </tr>
                </table>
                <h1 style="margin:0 0 6px; font-size:20px; font-weight:800; color:#2c1a30;">Hola, {{ $vacationRequest->empleado->name }} 👋</h1>
                <p style="margin:0; font-size:14px; color:#7a6682; line-height:1.6;">Tu solicitud de vacaciones ha sido <strong style="color:#2d7a4f;">aprobada</strong>. Aquí tienes los detalles de tu período vacacional.</p>
            </td></tr>

            {{-- Detalles --}}
            <tr><td style="background:#fff; padding:20px 36px 0;">
                <p style="margin:0 0 12px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#6A2C75;">Detalles de tus Vacaciones</p>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr><td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">📅 Fecha de Inicio</td>
                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_inicio)->format('d/m/Y') }}</td>
                        </tr></table>
                    </td></tr>
                    <tr><td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">📅 Fecha de Término</td>
                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_fin)->format('d/m/Y') }}</td>
                        </tr></table>
                    </td></tr>
                    <tr><td style="padding:8px 0; border-bottom:1px solid #f0e6f5;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">🔄 Fecha de Reincorporación</td>
                            <td style="font-size:13px; font-weight:600; color:#2c1a30; text-align:right;">{{ \Carbon\Carbon::parse($vacationRequest->fecha_reincorporacion)->format('d/m/Y') }}</td>
                        </tr></table>
                    </td></tr>
                    <tr><td style="padding:8px 0;">
                        <table width="100%"><tr>
                            <td style="font-size:13px; color:#7a6682;">🌴 Días Aprobados</td>
                            <td style="text-align:right;"><span style="background:#e8f5ee; color:#1b6b38; font-size:13px; font-weight:800; padding:2px 10px; border-radius:20px;">{{ $vacationRequest->dias_solicitados }} días</span></td>
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