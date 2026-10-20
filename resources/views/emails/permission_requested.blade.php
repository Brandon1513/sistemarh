{{-- permission_requested.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Nuevo Permiso Solicitado</title></head>
<body style="margin:0; padding:0; background:#f4eff7; font-family:'Segoe UI',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
    <tr><td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">
            <tr><td style="background:linear-gradient(135deg,#6A2C75 0%,#9b4dab 100%); border-radius:16px 16px 0 0; padding:28px 36px; text-align:center;">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; display:block; margin:0 auto 12px;">
                <p style="margin:0; font-size:13px; color:rgba(255,255,255,.75); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Sistema de Recursos Humanos</p>
            </td></tr>
            <tr><td style="background:#fff; padding:28px 36px 0;">
                <p style="margin:0 0 6px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#BBA4C0;">Nuevo Permiso</p>
                <h1 style="margin:0 0 8px; font-size:22px; font-weight:800; color:#2c1a30;">Pase de Salida Solicitado</h1>
                <p style="margin:0; font-size:14px; color:#7a6682; line-height:1.6;">
                    Hola <strong style="color:#2c1a30;">{{ $notifiable->name }}</strong>, tienes un nuevo permiso pendiente de revisión.
                </p>
            </td></tr>
            <tr><td style="background:#fff; padding:20px 36px 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3eef5; border-radius:10px; border:1px solid #e6d9ed; padding:16px;">
                    <tr>
                        <td style="width:40px; vertical-align:middle; text-align:center;">
                            <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#6A2C75,#BBA4C0); color:#fff; font-size:16px; font-weight:800; text-align:center; line-height:40px; display:inline-block;">
                                {{ strtoupper(substr($permission->user->name, 0, 1)) }}
                            </div>
                        </td>
                        <td style="padding-left:12px;">
                            <p style="margin:0; font-size:14px; font-weight:700; color:#2c1a30;">{{ $permission->user->name }}</p>
                            <p style="margin:2px 0 0; font-size:12px; color:#7a6682;">Ha solicitado un pase de salida</p>
                        </td>
                    </tr>
                </table>
            </td></tr>
            <tr><td style="background:#fff; padding:24px 36px;">
                <table width="100%"><tr><td align="center">
                    <a href="{{ url('/permissions/' . $permission->id) }}" style="display:inline-block; background:#6A2C75; color:#fff; text-decoration:none; font-size:14px; font-weight:700; padding:12px 28px; border-radius:9px;">Ver Permiso</a>
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