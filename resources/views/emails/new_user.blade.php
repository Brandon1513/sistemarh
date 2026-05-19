<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a {{ config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background:#f4eff7; font-family: 'Segoe UI', Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4eff7; padding:32px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#6A2C75 0%,#9b4dab 100%); border-radius:16px 16px 0 0; padding:32px 36px; text-align:center;">
                            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height:72px; width:auto; display:block; margin:0 auto 12px;">
                            <p style="margin:0; font-size:13px; color:rgba(255,255,255,.7); letter-spacing:.06em; text-transform:uppercase; font-weight:600;">Sistema de Recursos Humanos</p>
                        </td>
                    </tr>

                    {{-- Bienvenida --}}
                    <tr>
                        <td style="background:#fff; padding:32px 36px 0;">
                            <p style="margin:0 0 6px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#BBA4C0;">¡Bienvenido!</p>
                            <h1 style="margin:0 0 8px; font-size:24px; font-weight:800; color:#2c1a30;">Hola, {{ $user->name }} 👋</h1>
                            <p style="margin:0; font-size:14px; color:#7a6682; line-height:1.6;">
                                Tu cuenta ha sido creada exitosamente en la plataforma de <strong style="color:#6A2C75;">{{ config('app.name') }}</strong>. A continuación encontrarás tus credenciales de acceso.
                            </p>
                        </td>
                    </tr>

                    {{-- Credenciales --}}
                    <tr>
                        <td style="background:#fff; padding:20px 36px 0;">
                            <p style="margin:0 0 12px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#6A2C75;">Tus Credenciales</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3eef5; border-radius:10px; border:1px solid #e6d9ed; overflow:hidden;">
                                <tr>
                                    <td style="padding:14px 16px; border-bottom:1px solid #e6d9ed;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682;">📧 Correo electrónico</td>
                                            <td style="font-size:13px; font-weight:700; color:#2c1a30; text-align:right;">{{ $user->email }}</td>
                                        </tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 16px;">
                                        <table width="100%"><tr>
                                            <td style="font-size:13px; color:#7a6682;">🔑 Contraseña temporal</td>
                                            <td style="text-align:right;">
                                                <span style="background:#6A2C75; color:#fff; font-size:13px; font-weight:700; padding:4px 12px; border-radius:6px; font-family:monospace;">{{ $password }}</span>
                                            </td>
                                        </tr></table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Roles asignados --}}
                    <tr>
                        <td style="background:#fff; padding:20px 36px 0;">
                            <p style="margin:0 0 10px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#6A2C75;">Roles Asignados</p>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    @foreach($roles as $role)
                                    <td style="padding-right:6px;">
                                        <span style="display:inline-block; background:#f3eef5; color:#6A2C75; border:1px solid #e6d9ed; font-size:12px; font-weight:700; padding:4px 12px; border-radius:20px;">
                                            {{ $role->name }}
                                        </span>
                                    </td>
                                    @endforeach
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Aviso seguridad --}}
                    <tr>
                        <td style="background:#fff; padding:20px 36px 0;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf3e0; border:1.5px solid #EED39B; border-radius:10px; padding:12px 16px;">
                                <tr>
                                    <td style="font-size:20px; padding-right:10px; vertical-align:middle;">⚠️</td>
                                    <td>
                                        <p style="margin:0; font-size:13px; font-weight:700; color:#473524;">Cambia tu contraseña</p>
                                        <p style="margin:4px 0 0; font-size:12px; color:#7a6050;">Por seguridad, te recomendamos cambiar tu contraseña temporal en cuanto inicies sesión por primera vez.</p>
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
                                        <a href="{{ route('login') }}"
                                           style="display:inline-block; background:#6A2C75; color:#fff; text-decoration:none; font-size:14px; font-weight:700; padding:13px 32px; border-radius:9px; letter-spacing:.03em;">
                                            Iniciar Sesión →
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
                            <p style="margin:0; font-size:11px; color:#BBA4C0;">Si no esperabas este correo, por favor contáctanos.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>