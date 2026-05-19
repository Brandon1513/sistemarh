<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportación Semanal Pases de Salida</title>
</head>
<body style="margin:0; padding:0; background:#f4eff7; font-family: 'Segoe UI', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#6A2C75 0%,#9b4dab 100%); border-radius:16px 16px 0 0; padding:28px 36px; text-align:center;">
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; display:block; margin:0 auto 10px;">
                            <p style="margin:0; font-size:13px; color:rgba(255,255,255,.7); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Sistema de Recursos Humanos</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fff; padding:28px 36px 0;">
                            <p style="margin:0 0 6px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#BBA4C0;">Exportación Lista</p>
                            <h1 style="margin:0 0 8px; font-size:22px; font-weight:800; color:#2c1a30;">Exportación Semanal Lista 🚪</h1>
                            <p style="margin:0; font-size:14px; color:#7a6682; line-height:1.6;">La exportación semanal de pases de salida ha sido completada. Descarga el archivo desde el botón a continuación.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fff; padding:20px 36px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3eef5; border-radius:10px; border:1px solid #e6d9ed; padding:16px;">
                                <tr>
                                    <td style="font-size:22px; padding-right:12px; vertical-align:middle;">🚪</td>
                                    <td style="vertical-align:middle;">
                                        <p style="margin:0; font-size:13px; font-weight:700; color:#2c1a30;">Archivo Excel — Pases de Salida Semana Nominal</p>
                                        <p style="margin:2px 0 0; font-size:12px; color:#7a6682;">El archivo estará disponible por tiempo limitado.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#fff; padding:24px 36px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}" style="display:inline-block; background:#6A2C75; color:#fff; text-decoration:none; font-size:14px; font-weight:700; padding:13px 32px; border-radius:9px; letter-spacing:.03em;">
                                            Descargar Pases de Salida
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f7f3ef; border-top:1.5px solid #e6d9ed; border-radius:0 0 16px 16px; padding:18px 36px; text-align:center;">
                            <p style="margin:0 0 4px; font-size:12px; color:#7a6682;">Este correo fue generado automáticamente por <strong style="color:#6A2C75;">{{ config('app.name') }}</strong></p>
                            <p style="margin:0; font-size:11px; color:#BBA4C0;">Si no solicitaste esta exportación, puedes ignorar este correo.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>