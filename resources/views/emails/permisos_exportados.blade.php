<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Dasavena</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; color: #718096; height: 100%; margin: 0; padding: 0; width: 100% !important;">

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #edf2f7; margin: 0; padding: 0; width: 100%;">
    <tr>
        <td align="center">
            <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td class="header" style="padding: 25px 0; text-align: center;">
                        <a href="{{ config('app.url') }}" style="color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none;">
                            <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Dasavena Logo" style="height: 75px;">
                        </a>
                    </td>
                </tr>

                <!-- Email Body -->
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0" style="background-color: #edf2f7; border: hidden !important; margin: 0; padding: 0;">
                        <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #ffffff; border-radius: 2px; margin: 0 auto; padding: 0; width: 570px;">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell" style="padding: 32px;">
                                    <h1 style="color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0;">Exportación Libro Mayor</h1>
                                    <p style="font-size: 16px; line-height: 1.5em; margin-top: 0;">La exportación libro mayor de permisos ha sido completada. Puedes descargar el archivo desde el siguiente enlace:</p>
                                    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 30px auto; text-align: center;">
                                        <tr>
                                            <td align="center">
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                    <tr>
                                                        <td align="center">
                                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                                                <tr>
                                                                    <td>
                                                                        <a href="{{ $url }}" class="button button-primary" target="_blank" rel="noopener" style="border-radius: 4px; color: #fff; text-decoration: none; background-color: #2d3748; border: 8px solid #2d3748;">Descargar Libro Mayor</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="font-size: 16px; line-height: 1.5em; margin-top: 0;">Si no solicitaste esta exportación, por favor ignora este mensaje.</p>
                                    <p style="font-size: 16px; line-height: 1.5em; margin-top: 0;">Gracias,<br>Dasavena</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td>
                        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto; text-align: center; width: 570px;">
                            <tr>
                                <td class="content-cell" align="center" style="padding: 32px;">
                                    <p style="line-height: 1.5em; color: #b0adc5; font-size: 12px;">© 2024 Dasavena. Todos los derechos reservados.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
