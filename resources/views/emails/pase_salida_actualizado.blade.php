<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Estado de Pase de Salida</title></head>
<body style="margin:0; padding:0; background:#f4eff7; font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
    <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

            <tr><td style="background:linear-gradient(135deg,#6A2C75 0%,#9b4dab 100%); border-radius:16px 16px 0 0; padding:28px 36px; text-align:center;">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; display:block; margin:0 auto 12px;">
                <p style="margin:0; font-size:13px; color:rgba(255,255,255,.75); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Sistema de Recursos Humanos</p>
            </td></tr>

            <tr><td style="background:#fff; padding:28px 36px 0;">
                <p style="margin:0 0 6px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#BBA4C0;">Actualización</p>
                <h1 style="margin:0 0 8px; font-size:22px; font-weight:800; color:#2c1a30;">Estado de Pase de Salida</h1>
                <p style="margin:0; font-size:14px; color:#7a6682;">Tu solicitud de pase de salida ha sido revisada.</p>
            </td></tr>

            <tr><td style="background:#fff; padding:20px 36px 0;">
                @if($estado === 'aprobado')
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#e8f5ee; border:1.5px solid #b3dfc3; border-radius:10px; padding:16px;">
                    <tr>
                        <td style="font-size:28px; padding-right:14px; vertical-align:middle;">✅</td>
                        <td>
                            <p style="margin:0; font-size:16px; font-weight:800; color:#1b6b38;">Pase de Salida Aprobado</p>
                            <p style="margin:4px 0 0; font-size:13px; color:#2d7a4f;">Tu pase de salida ha sido aprobado por tu jefe directo.</p>
                        </td>
                    </tr>
                </table>
                @else
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#fce8ee; border:1.5px solid #f0b3c3; border-radius:10px; padding:16px;">
                    <tr>
                        <td style="font-size:28px; padding-right:14px; vertical-align:middle;">❌</td>
                        <td>
                            <p style="margin:0; font-size:16px; font-weight:800; color:#7a2039;">Pase de Salida Rechazado</p>
                            <p style="margin:4px 0 0; font-size:13px; color:#AA4969;">Tu pase de salida no fue aprobado esta vez.</p>
                        </td>
                    </tr>
                </table>
                @endif
            </td></tr>

            <tr><td style="background:#fff; padding:20px 36px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf3e0; border:1.5px solid #EED39B; border-radius:10px; padding:12px 16px;">
                    <tr>
                        <td style="font-size:18px; padding-right:10px; vertical-align:middle;">💡</td>
                        <td><p style="margin:0; font-size:13px; color:#473524; line-height:1.5;">Si tienes dudas sobre esta decisión, por favor contacta directamente a tu jefe o al área de <strong>Recursos Humanos</strong>.</p></td>
                    </tr>
                </table>
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