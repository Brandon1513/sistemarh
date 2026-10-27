{{-- Plantilla oficial de los avisos de RH. El formulario la llena y la convierte en imagen. --}}
<style>
    @font-face {
        font-family: 'Veneer';
        src: url('{{ asset('fonts/Veneer.ttf') }}') format('truetype');
        font-display: block;
    }
    #plantilla-aviso {
        position: fixed; left: -12000px; top: 0;   /* fuera de la vista, pero dibujada */
        width: 1080px; background: #ffffff; color: #333333;
        padding: 40px 56px 48px; box-sizing: border-box;
        font-family: Calibri, 'Segoe UI', Arial, sans-serif;
    }
    #plantilla-aviso * { box-sizing: border-box; }
    #plantilla-aviso .pa-cabecera { display: flex; align-items: center; gap: 18px; }
    #plantilla-aviso .pa-logo { width: 140px; height: 140px; flex: none; }
    #plantilla-aviso .pa-titulos { flex: 1; text-align: center; }
    #plantilla-aviso .pa-encabezado { font-family: 'Veneer', Impact, sans-serif; font-size: 76px; line-height: 1; color: #c0141c; letter-spacing: 1px; }
    #plantilla-aviso .pa-titulo { font-family: 'Veneer', Impact, sans-serif; font-size: 44px; line-height: 1.15; color: #6A2C75; text-transform: uppercase; margin-top: 14px; }
    #plantilla-aviso .pa-cuerpo { margin-top: 36px; font-size: 25px; line-height: 1.5; text-align: justify; }
    #plantilla-aviso .pa-cuerpo p { margin: 0 0 22px; }
    #plantilla-aviso .pa-cuerpo strong { color: #222222; }
    #plantilla-aviso .pa-firma { margin-top: 18px; text-align: right; font-size: 25px; font-weight: bold; color: #555555; }
    #plantilla-aviso .pa-firma > div { display: inline-block; text-align: center; }
</style>

<div id="plantilla-aviso" aria-hidden="true">
    <div class="pa-cabecera">
        <img class="pa-logo" src="{{ asset('images/logo.png') }}" alt="">
        <div class="pa-titulos">
            <div class="pa-encabezado" id="pa-encabezado">AVISO IMPORTANTE</div>
            <div class="pa-titulo" id="pa-titulo"></div>
        </div>
    </div>
    <div class="pa-cuerpo" id="pa-cuerpo"></div>
    <div class="pa-firma">
        <div>
            <div style="margin-bottom:14px;">Atentamente:</div>
            <div id="pa-firma">Recursos Humanos</div>
        </div>
    </div>
</div>