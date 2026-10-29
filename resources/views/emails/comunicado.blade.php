@php
    $c = $comunicado;
    $rutaImagen = $c->imagen ? storage_path('app/public/' . $c->imagen) : null;
    $rutaLogo = is_file(public_path('images/logo-correo.png'))
    ? public_path('images/logo-correo.png')
    : public_path('images/logo.png');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $c->titulo }}</title>
</head>
<body style="margin:0; padding:0; background:#f3eef5; font-family:'Segoe UI', Arial, sans-serif; color:#2c1a30;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3eef5; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:14px; overflow:hidden;">

                    {{-- Encabezado morado con el logo --}}
                    <tr>
                        <td style="background:#6A2C75; padding:18px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="52">
                                        @if(is_file($rutaLogo))
                                            <img src="{{ $message->embed($rutaLogo) }}" width="44" height="44" alt="Dasavena" style="display:block; border-radius:50%;">
                                        @endif
                                    </td>
                                    <td style="color:#EED39B; font-size:13px; font-weight:bold; letter-spacing:1px; text-transform:uppercase;">
                                        {{ $c->tipo === 'aviso' ? 'Recursos Humanos' : 'Intranet Dasavena' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Imagen principal (en los avisos, la de la plantilla) --}}
                    @if($rutaImagen && is_file($rutaImagen))
                        <tr>
                            <td style="padding:0;">
                                <img src="{{ $message->embed($rutaImagen) }}" width="600" alt="{{ $c->titulo }}" style="display:block; width:100%; max-width:600px; height:auto;">
                            </td>
                        </tr>
                    @endif

                    {{-- Título y texto. En los avisos con plantilla no se repite: la imagen ya lo trae --}}
                    @if(! ($c->usar_plantilla && $rutaImagen && is_file($rutaImagen)))
                        <tr>
                            <td style="padding:26px 28px 8px;">
                                <h1 style="margin:0 0 6px; font-size:22px; line-height:1.3; color:#3d1545;">{{ $c->titulo }}</h1>
                                <p style="margin:0 0 18px; font-size:13px; color:#7a6682;">{{ $c->firma }} · {{ ($c->publicado_en ?? now())->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</p>
                                <div style="font-size:15px; line-height:1.65; color:#3d2943;">
                                    {{ \App\Support\TextoComunicado::html($c->contenido) }}
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr><td style="padding:18px 28px 0;"></td></tr>
                    @endif

                    {{-- Botones --}}
                    <tr>
                        <td style="padding:8px 28px 28px;">
                            @if($c->enlace)
                                <a href="{{ $c->enlace }}" style="display:inline-block; background:#D6A644; color:#473524; text-decoration:none; font-weight:bold; font-size:14px; padding:11px 20px; border-radius:8px; margin:0 8px 8px 0;">
                                    {{ $c->tipo === 'red_social' ? 'Ver la publicación' : 'Abrir enlace' }}
                                </a>
                            @endif
                            <a href="{{ route('noticias.public') }}" style="display:inline-block; background:#6A2C75; color:#ffffff; text-decoration:none; font-weight:bold; font-size:14px; padding:11px 20px; border-radius:8px; margin:0 0 8px;">
                                Ver en la intranet
                            </a>
                        </td>
                    </tr>

                    {{-- Pie --}}
                    <tr>
                        <td style="padding:16px 28px; background:#f7f3ef; font-size:12px; color:#7a6682; line-height:1.5;">
                            Este comunicado también está en la Intranet y en la app de Dasavena.<br>
                            Si tienes dudas, responde a este correo.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>