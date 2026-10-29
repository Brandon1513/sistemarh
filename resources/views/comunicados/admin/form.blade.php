@php
    use App\Models\Comunicado;

    $editando = $comunicado->exists;
    $tipo = old('tipo', $comunicado->tipo);
    $iconosTipo = ['noticia' => '📰', 'red_social' => '📱', 'aviso' => '📢'];

    // "Necesita revisión" viene marcado si regresó con errores pidiendo revisión,
    // o si se lo devolvieron (para reenviarlo al mismo revisor)
    $pideRevision = old('accion') === 'revision' || (! old() && $comunicado->estado === 'devuelto');

    // Ahora o programado
    $cuando = old('cuando', $comunicado->publicar_en ? 'programar' : 'ahora');
    $fechaProgramada = old('publicar_en', $comunicado->publicar_en?->format('Y-m-d\TH:i'));
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('comunicados.admin.index') }}" style="color:#BBA4C0;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $editando ? 'Editar' : 'Nuevo' }} · {{ $iconosTipo[$tipo] ?? '' }} {{ Comunicado::TIPOS[$tipo] ?? 'Comunicado' }}
            </h2>
            @if($editando)
                <span class="px-3 py-1 text-xs font-bold rounded-full" style="background:#f3eef5; color:#6A2C75;">{{ $comunicado->estado_nombre }}</span>
            @endif
        </div>
    </x-slot>

    <style>
        :root {
            --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0;
            --gold:#D6A644; --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef;
            --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682;
        }
        .layout { display:grid; grid-template-columns:minmax(0,1fr) 320px; gap:20px; align-items:start; }
        @media(max-width:960px){ .layout { grid-template-columns:minmax(0,1fr); } }
        .columna { display:flex; flex-direction:column; gap:20px; min-width:0; }
        .card { background:#fff; border-radius:16px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); }
        .card-body { padding:26px 28px; display:flex; flex-direction:column; gap:20px; }
        .card-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); }
        .field { display:flex; flex-direction:column; gap:5px; }
        .field[hidden] { display:none; }
        .label { font-size:13px; font-weight:600; color:var(--brown); }
        .req { color:var(--rose); }
        .opt { font-size:11px; font-weight:400; color:var(--muted); background:#f0e6f5; border-radius:4px; padding:1px 7px; margin-left:4px; }
        .input, .select, .textarea { width:100%; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; padding:0 14px; }
        .input, .select { height:42px; }
        .textarea { padding:12px 14px; line-height:1.6; resize:vertical; }
        .input:focus, .select:focus, .textarea:focus { border-color:var(--brand); outline:none; box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .titulo-input { height:52px; font-size:19px; font-weight:700; }
        .hint { font-size:12px; color:var(--muted); }
        .contador { font-size:11.5px; color:var(--muted); text-align:right; }

        .imagen-zona { position:relative; border:2px dashed var(--border); border-radius:12px; overflow:hidden; background:var(--beige-light); min-height:170px; display:flex; align-items:center; justify-content:center; text-align:center; cursor:pointer; }
        .imagen-zona:hover { border-color:var(--brand); }
        .imagen-zona input { position:absolute; inset:0; opacity:0; cursor:pointer; }
        .imagen-zona img { width:100%; height:auto; max-height:420px; object-fit:contain; background:#fff; }
        .imagen-zona .vacia { padding:20px; color:var(--muted); font-size:13px; }
        .generando { position:absolute; top:8px; right:8px; font-size:11px; font-weight:700; color:var(--brand); background:#fff; border:1px solid var(--border); border-radius:20px; padding:3px 9px; display:none; }
        .generando.show { display:block; }

        .galeria { display:grid; grid-template-columns:repeat(auto-fill, minmax(96px,1fr)); gap:10px; }
        .foto { position:relative; aspect-ratio:1; border-radius:10px; overflow:hidden; background:var(--brand-light); }
        .foto img { width:100%; height:100%; object-fit:cover; }
        .foto label { position:absolute; inset:auto 4px 4px 4px; display:flex; align-items:center; justify-content:center; gap:4px; background:rgba(28,6,34,.7); color:#fff; font-size:11px; font-weight:600; border-radius:6px; padding:3px; cursor:pointer; }
        .foto.quitada img { opacity:.25; }
        .agregar-fotos { position:relative; aspect-ratio:1; border:2px dashed var(--border); border-radius:10px; display:flex; flex-direction:column; align-items:center; justify-content:center; font-size:12px; color:var(--muted); cursor:pointer; text-align:center; }
        .agregar-fotos:hover { border-color:var(--brand); color:var(--brand); }
        .agregar-fotos input { position:absolute; inset:0; opacity:0; cursor:pointer; }

        .check { display:flex; align-items:flex-start; gap:10px; padding:12px 14px; border:1.5px solid var(--border); border-radius:9px; cursor:pointer; }
        .check input { width:18px; height:18px; margin-top:2px; accent-color:var(--brand); }
        .check:has(input:checked) { border-color:var(--brand); background:var(--brand-light); }

        /* ── Opciones tipo radio (destinatarios y cuándo) ── */
        .para-opciones { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .para-opcion { display:flex; align-items:center; gap:10px; padding:14px; border:1.5px solid var(--border); border-radius:12px; cursor:pointer; }
        .para-opcion input { width:18px; height:18px; accent-color:var(--brand); }
        .para-opcion:has(input:checked) { border-color:var(--brand); background:var(--brand-light); }
        .para-opcion strong { display:block; font-size:14px; color:var(--text); }
        .para-opcion span { font-size:12px; color:var(--muted); }
        .para-opciones.compactas .para-opcion { padding:10px 12px; }

        /* ── Destinatarios ── */
        .elegir-panel { display:flex; flex-direction:column; gap:18px; }
        .elegir-panel[hidden] { display:none; }
        .subtitulo { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); margin-bottom:8px; }
        .chips-opciones { display:flex; flex-wrap:wrap; gap:8px; }
        .chip-opcion { display:inline-flex; align-items:center; gap:6px; padding:7px 12px; border:1.5px solid var(--border); border-radius:20px; font-size:13px; font-weight:600; color:var(--text); background:#fff; cursor:pointer; user-select:none; }
        .chip-opcion input { display:none; }
        .chip-opcion:has(input:checked) { background:var(--brand); border-color:var(--brand); color:#fff; }
        .chip-opcion small { font-weight:500; opacity:.7; }
        .buscador { position:relative; }
        .resultados { position:absolute; left:0; right:0; top:calc(100% + 4px); background:#fff; border:1px solid var(--border); border-radius:10px; box-shadow:0 8px 24px rgba(61,21,69,.18); max-height:280px; overflow-y:auto; z-index:20; display:none; }
        .resultados.show { display:block; }
        .resultado { display:block; width:100%; text-align:left; padding:10px 14px; border:none; background:none; cursor:pointer; border-bottom:1px solid #f5f0f7; }
        .resultado:hover { background:var(--brand-light); }
        .resultado strong { display:block; font-size:13.5px; color:var(--text); }
        .resultado small { font-size:12px; color:var(--muted); }
        .resultado-vacio { padding:12px 14px; font-size:13px; color:var(--muted); }
        .personas-elegidas { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
        .persona-chip { display:inline-flex; align-items:center; gap:6px; padding:5px 6px 5px 12px; border-radius:20px; background:var(--brand-light); color:var(--brand); font-size:13px; font-weight:600; }
        .persona-chip button { width:20px; height:20px; border-radius:50%; border:none; background:rgba(106,44,117,.15); color:var(--brand); cursor:pointer; font-size:12px; line-height:1; }
        .alcance { display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:10px; background:var(--beige-light); font-size:13.5px; color:var(--text); }
        .alcance strong { color:var(--brand); font-size:16px; }

        .btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:0 20px; height:44px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; width:100%; }
        .btn[hidden] { display:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); }
        .btn-gold { background:var(--gold); color:#fff; }
        .btn-outline { background:#fff; color:var(--brand); border:1.5px solid var(--brand-mid); }
        .btn-outline:hover { border-color:var(--brand); }
        .alert { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; }
        .alert-success { background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }
        .alert-warning { background:#faf3e0; color:#7a5a12; border:1px solid #EED39B; }
        .alert-error { background:#fce8ee; color:#7a2039; border:1px solid #f0b3c3; }
        .alert-devuelto { background:#fce8ee; color:#7a2039; border:1px solid #f0b3c3; border-left:4px solid var(--rose); }
        .alert-programado { background:#e0eaff; color:#2b4c9b; border:1px solid #b8ccf5; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">⚠ {{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Revisa lo siguiente:</strong>
                    <ul style="margin:6px 0 0 18px; list-style:disc;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Si se lo devolvieron, el comentario del revisor va hasta arriba --}}
            @if($comunicado->estado === 'devuelto' && $comunicado->comentario_revision)
                <div class="alert alert-devuelto">
                    <strong>↩ {{ $comunicado->revisor?->name ?? 'RH' }} te lo devolvió con estos comentarios:</strong>
                    <p style="margin-top:6px; white-space:pre-line;">{{ $comunicado->comentario_revision }}</p>
                    <p style="margin-top:8px; font-size:12.5px;">Corrígelo y vuelve a enviarlo a revisión.</p>
                </div>
            @endif

            {{-- Si está programado, se recuerda cuándo sale --}}
            @if($comunicado->estado === 'programado' && $comunicado->publicar_en)
                <div class="alert alert-programado">
                    ⏰ Este comunicado se publicará solo el <strong>{{ $comunicado->publicar_en->locale('es')->isoFormat('dddd D [de] MMMM [a las] H:mm') }}</strong>.
                    Si lo guardas como borrador, deja de estar programado.
                </div>
            @endif

            <form method="POST" enctype="multipart/form-data" id="form-comunicado"
                  action="{{ $editando ? route('comunicados.admin.update', $comunicado) : route('comunicados.admin.store') }}">
                @csrf
                @if($editando) @method('PUT') @endif
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                {{-- La acción viaja aquí y no en el botón: el bloqueo de doble envío desactiva los botones --}}
                <input type="hidden" name="accion" id="accion" value="borrador">
                {{-- La imagen que genera la plantilla de RH al guardar --}}
                <input type="file" name="imagen_generada" id="imagen_generada" accept="image/png" hidden>

                <div class="layout">
                    {{-- ── Columna principal ── --}}
                    <div class="columna">
                        <div class="card">
                            <div class="card-body">
                                <div class="field">
                                    <label class="label" for="titulo">Título <span class="req">*</span></label>
                                    <input id="titulo" name="titulo" class="input titulo-input" maxlength="255" required
                                           value="{{ old('titulo', $comunicado->titulo) }}"
                                           placeholder="{{ $tipo === 'aviso' ? 'Ej. Pago de nómina semana 42 (viernes 23 de octubre)' : 'Ej. Lanzamos nuestra nueva línea de granolas' }}">
                                </div>

                                <div class="field">
                                    <label class="label" for="resumen">Resumen <span class="opt">Opcional</span></label>
                                    <input id="resumen" name="resumen" class="input" maxlength="300" data-contador="contador-resumen"
                                           value="{{ old('resumen', $comunicado->resumen) }}"
                                           placeholder="Una o dos líneas que aparecen en la lista y en la notificación">
                                    <span class="contador" id="contador-resumen"></span>
                                    <span class="hint">Si lo dejas vacío, se toma el inicio del contenido.</span>
                                </div>

                                <div class="field">
                                    <label class="label" for="contenido">
                                        Contenido @if($tipo !== 'red_social')<span class="req">*</span>@else<span class="opt">Opcional</span>@endif
                                    </label>
                                    <textarea id="contenido" name="contenido" class="textarea" rows="14" maxlength="20000" data-contador="contador-contenido"
                                              placeholder="Escribe el comunicado. Deja una línea en blanco entre párrafos.">{{ old('contenido', $comunicado->contenido) }}</textarea>
                                    <span class="contador" id="contador-contenido"></span>
                                    <span class="hint">Para <strong>negritas</strong>, escribe el texto entre dos asteriscos: **así**.</span>
                                </div>

                                <div class="field">
                                    <label class="label" for="enlace">
                                        {{ $tipo === 'red_social' ? 'Liga de la publicación' : 'Liga' }}
                                        @if($tipo === 'red_social')<span class="req">*</span>@else<span class="opt">Opcional</span>@endif
                                    </label>
                                    <input id="enlace" name="enlace" type="url" class="input" maxlength="255"
                                           value="{{ old('enlace', $comunicado->enlace) }}"
                                           placeholder="{{ $tipo === 'red_social' ? 'https://www.instagram.com/p/...' : 'https://... (video, formulario, sitio)' }}">
                                </div>

                                {{-- Galería --}}
                                <div class="field">
                                    <span class="label">Galería <span class="opt">Opcional · hasta 20 fotos de 2 MB</span></span>
                                    <div class="galeria" id="galeria">
                                        @foreach($comunicado->imagenes ?? [] as $foto)
                                            <div class="foto">
                                                <img src="{{ $foto->url }}" alt="">
                                                <label>
                                                    <input type="checkbox" name="quitar_galeria[]" value="{{ $foto->id }}"
                                                           onchange="this.closest('.foto').classList.toggle('quitada', this.checked)">
                                                    Quitar
                                                </label>
                                            </div>
                                        @endforeach
                                        <label class="agregar-fotos">
                                            <span style="font-size:22px;">＋</span> Agregar fotos
                                            <input type="file" name="galeria[]" accept="image/jpeg,image/png,image/webp" multiple id="galeria-input">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Destinatarios ── --}}
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">¿A quién va dirigido?</p>

                                <div class="para-opciones">
                                    <label class="para-opcion">
                                        <input type="radio" name="para" value="todos" {{ $para === 'todos' ? 'checked' : '' }}>
                                        <div><strong>Para todos</strong><span>Toda la empresa</span></div>
                                    </label>
                                    <label class="para-opcion">
                                        <input type="radio" name="para" value="elegir" {{ $para === 'elegir' ? 'checked' : '' }}>
                                        <div><strong>Elegir destinatarios</strong><span>Sedes, áreas, grupos o personas</span></div>
                                    </label>
                                </div>

                                <div class="elegir-panel" id="elegir-panel" {{ $para === 'elegir' ? '' : 'hidden' }}>
                                    <div>
                                        <p class="subtitulo">Sedes</p>
                                        <div class="chips-opciones">
                                            @foreach($sedes as $sede)
                                                <label class="chip-opcion">
                                                    <input type="checkbox" name="destinos[]" value="sede:{{ $sede->id }}" {{ in_array("sede:{$sede->id}", $elegidos) ? 'checked' : '' }}>
                                                    {{ $sede->nombre }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <p class="subtitulo">Departamentos</p>
                                        <div class="chips-opciones">
                                            @foreach($departamentos as $dep)
                                                <label class="chip-opcion">
                                                    <input type="checkbox" name="destinos[]" value="departamento:{{ $dep->id }}" {{ in_array("departamento:{$dep->id}", $elegidos) ? 'checked' : '' }}>
                                                    {{ $dep->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <p class="subtitulo">Grupos</p>
                                        @if($grupos->isEmpty())
                                            <p class="hint">Todavía no hay grupos. RH los crea en Administración → Grupos.</p>
                                        @else
                                            <div class="chips-opciones">
                                                @foreach($grupos as $grupo)
                                                    <label class="chip-opcion" @unless($grupo->activo) title="Este grupo está inactivo" @endunless>
                                                        <input type="checkbox" name="destinos[]" value="grupo:{{ $grupo->id }}" {{ in_array("grupo:{$grupo->id}", $elegidos) ? 'checked' : '' }}>
                                                        {{ $grupo->nombre }} <small>{{ $grupo->usuarios_count }}</small>
                                                        @unless($grupo->activo)<small>· inactivo</small>@endunless
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="subtitulo">Personas en particular</p>
                                        <div class="buscador">
                                            <input type="search" id="buscar-persona" class="input" autocomplete="off"
                                                   placeholder="Escribe un nombre, correo o clave de empleado...">
                                            <div class="resultados" id="resultados-personas"></div>
                                        </div>
                                        <div class="personas-elegidas" id="personas-elegidas">
                                            @foreach($personas as $persona)
                                                <span class="persona-chip" data-id="{{ $persona['id'] }}">
                                                    {{ $persona['nombre'] }}
                                                    <input type="hidden" name="destinos[]" value="usuario:{{ $persona['id'] }}">
                                                    <button type="button" title="Quitar" data-quitar-persona>✕</button>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="alcance">
                                    👥 <span>Le llegará a <strong id="total-destinatarios">…</strong> <span id="palabra-personas">personas</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Panel lateral ── --}}
                    <div class="columna">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Publicación</p>

                                @if($comunicado->estado !== 'publicado')
                                    {{-- Ahora o programado --}}
                                    <div class="para-opciones compactas">
                                        <label class="para-opcion">
                                            <input type="radio" name="cuando" value="ahora" {{ $cuando === 'ahora' ? 'checked' : '' }}>
                                            <div><strong>Ahora</strong></div>
                                        </label>
                                        <label class="para-opcion">
                                            <input type="radio" name="cuando" value="programar" {{ $cuando === 'programar' ? 'checked' : '' }}>
                                            <div><strong>⏰ Programar</strong></div>
                                        </label>
                                    </div>

                                    <div class="field" id="campo-fecha" hidden>
                                        <label class="label" for="publicar_en">Fecha y hora</label>
                                        <input type="datetime-local" id="publicar_en" name="publicar_en" class="input"
                                               value="{{ $fechaProgramada }}" min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                                        @error('publicar_en')<span class="hint" style="color:var(--rose);">⚠ {{ $message }}</span>@enderror
                                    </div>
                                @endif

                                @if($comunicado->estado !== 'publicado' && $revisores->isNotEmpty())
                                    <label class="check">
                                        <input type="checkbox" id="necesita-revision" {{ $pideRevision ? 'checked' : '' }}>
                                        <span style="font-size:13.5px;"><strong>🔎 Necesita revisión</strong><br><span class="hint">Una persona de RH lo revisa antes de publicarlo.</span></span>
                                    </label>

                                    <div class="field" id="campo-revisor" hidden>
                                        <label class="label" for="revisor_id">¿Quién lo revisa?</label>
                                        <select id="revisor_id" name="revisor_id" class="select">
                                            <option value="">Elige a alguien de RH…</option>
                                            @foreach($revisores as $revisor)
                                                <option value="{{ $revisor->id }}" {{ (string) old('revisor_id', $comunicado->revisor_id) === (string) $revisor->id ? 'selected' : '' }}>{{ $revisor->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('revisor_id')<span class="hint" style="color:var(--rose);">⚠ {{ $message }}</span>@enderror
                                    </div>

                                    <button type="submit" class="btn btn-gold" id="btn-revision" hidden
                                            onclick="document.getElementById('accion').value = 'revision';">
                                        🔎 Enviar a revisión
                                    </button>
                                @endif

                                <button type="submit" class="btn btn-primary" id="btn-publicar"
                                        data-confirmar="Al publicarlo, lo verán sus destinatarios. ¿Continuar?"
                                        onclick="if (!confirm(this.dataset.confirmar)) return false; document.getElementById('accion').value = 'publicar';">
                                    {{ $comunicado->estado === 'publicado' ? 'Guardar cambios' : 'Publicar ahora' }}
                                </button>

                                @if($comunicado->estado !== 'publicado')
                                    <button type="submit" class="btn btn-outline" formnovalidate
                                            onclick="document.getElementById('accion').value = 'borrador';">
                                        Guardar borrador
                                    </button>
                                @endif

                                <label class="check" style="padding:10px 12px;">
                                    <input type="hidden" name="enviar_push" value="0">
                                    <input type="checkbox" name="enviar_push" value="1" {{ old('enviar_push', $comunicado->enviar_push ?? true) ? 'checked' : '' }}>
                                    <span style="font-size:13px;"><strong>🔔 Enviar notificación push</strong><br><span class="hint">A los destinatarios que tienen la app, al publicarse.</span></span>
                                </label>

                                <label class="check" style="padding:10px 12px;">
                                    <input type="hidden" name="enviar_correo" value="0">
                                    <input type="checkbox" name="enviar_correo" value="1" {{ old('enviar_correo', ($comunicado->enviar_correo ?? 'automatico') !== 'ninguno') ? 'checked' : '' }}>
                                    <span style="font-size:13px;"><strong>📧 Enviar por correo</strong><br><span class="hint" id="resumen-correo">Calculando…</span></span>
                                </label>

                                @if($comunicado->estado === 'publicado' && $comunicado->publicado_en)
                                    <span class="hint">Publicado el {{ $comunicado->publicado_en->locale('es')->isoFormat('D [de] MMMM [a las] H:mm') }}. La push ya se envió; los cambios no la repiten.</span>
                                @endif
                                @if($comunicado->correo_enviado_en)
    <span class="hint">📧 El correo se envió el {{ $comunicado->correo_enviado_en->locale('es')->isoFormat('D [de] MMMM [a las] H:mm') }} desde {{ $comunicado->remitente }}.</span>
@endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Imagen principal</p>
                                <label class="imagen-zona" id="imagen-zona">
                                    <span class="generando" id="generando">Generando…</span>
                                    @if($comunicado->imagen_url)
                                        <img src="{{ $comunicado->imagen_url }}" id="imagen-preview" alt="">
                                    @else
                                        <span class="vacia" id="imagen-vacia">📷<br>Toca para elegir una imagen<br><small>JPG, PNG o WEBP de hasta 2 MB</small></span>
                                        <img id="imagen-preview" alt="" style="display:none;">
                                    @endif
                                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" id="imagen-input">
                                </label>
                                @if($tipo === 'aviso')
                                    <span class="hint" id="hint-plantilla">Con la plantilla activada, aquí ves cómo va quedando el aviso mientras escribes.</span>
                                @endif
                                @if($comunicado->imagen)
                                    <label class="check" style="padding:8px 12px;">
                                        <input type="checkbox" name="quitar_imagen" value="1"> <span style="font-size:13px;">Quitar la imagen</span>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Detalles</p>

                                @if($tipo === 'aviso')
                                    <div class="field">
                                        <label class="label" for="categoria">Categoría</label>
                                        <select id="categoria" name="categoria" class="select">
                                            @foreach(Comunicado::CATEGORIAS_AVISO as $valor => $nombre)
                                                <option value="{{ $valor }}" {{ old('categoria', $comunicado->categoria ?? 'general') === $valor ? 'selected' : '' }}>{{ $nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="field">
                                        <label class="label" for="encabezado">Encabezado de la imagen</label>
                                        <select id="encabezado" name="encabezado" class="select">
                                            @foreach(Comunicado::ENCABEZADOS as $opcion)
                                                <option value="{{ $opcion }}" {{ old('encabezado', $comunicado->encabezado ?? 'AVISO IMPORTANTE') === $opcion ? 'selected' : '' }}>{{ $opcion }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <label class="check">
                                        <input type="hidden" name="usar_plantilla" value="0">
                                        <input type="checkbox" name="usar_plantilla" value="1" {{ old('usar_plantilla', $comunicado->usar_plantilla) ? 'checked' : '' }}>
                                        <span style="font-size:13.5px;"><strong>🖼 Generar imagen con la plantilla de RH</strong><br><span class="hint">Logo, encabezado, texto y firma. Se actualiza en cada guardado.</span></span>
                                    </label>
                                @endif

                                <div class="field">
                                    <label class="label" for="firma">Firma</label>
                                    <input id="firma" name="firma" class="input" maxlength="100" value="{{ old('firma', $comunicado->firma) }}">
                                    <span class="hint">Cómo aparece el autor ante los empleados.</span>
                                </div>

                                <label class="check">
                                    <input type="hidden" name="fijado" value="0">
                                    <input type="checkbox" name="fijado" value="1" {{ old('fijado', $comunicado->fijado) ? 'checked' : '' }}>
                                    <span style="font-size:13.5px;"><strong>📌 Fijar arriba</strong><br><span class="hint">Aparece primero hasta que lo quites o lo archives.</span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($tipo === 'aviso')
        @include('comunicados.plantillas.aviso-rh')
        <script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.js"></script>
    @endif

    <script>
        // ── Contadores de caracteres ──
        document.querySelectorAll('[data-contador]').forEach(el => {
            const salida = document.getElementById(el.dataset.contador);
            const pintar = () => { salida.textContent = `${el.value.length} / ${el.maxLength}`; };
            el.addEventListener('input', pintar);
            pintar();
        });

        // ── Vista previa de la imagen principal subida a mano ──
        document.getElementById('imagen-input').addEventListener('change', e => {
            const archivo = e.target.files[0];
            if (!archivo) return;
            const img = document.getElementById('imagen-preview');
            img.src = URL.createObjectURL(archivo);
            img.style.display = 'block';
            document.getElementById('imagen-vacia')?.remove();
        });

        // ── Vista previa de las fotos nuevas de la galería ──
        document.getElementById('galeria-input').addEventListener('change', e => {
            const galeria = document.getElementById('galeria');
            galeria.querySelectorAll('.foto.nueva').forEach(f => f.remove());
            [...e.target.files].forEach(archivo => {
                const foto = document.createElement('div');
                foto.className = 'foto nueva';
                foto.innerHTML = `<img src="${URL.createObjectURL(archivo)}" alt="">`;
                galeria.insertBefore(foto, galeria.lastElementChild);
            });
        });

        // ── Ahora o programar: muestra la fecha y cambia el texto del botón ──
        (() => {
            const radios = document.querySelectorAll('input[name=cuando]');
            if (!radios.length) return;

            const campo    = document.getElementById('campo-fecha');
            const fecha    = document.getElementById('publicar_en');
            const publicar = document.getElementById('btn-publicar');

            function aplicar() {
                const programar = document.querySelector('input[name=cuando]:checked')?.value === 'programar';
                campo.hidden = !programar;
                fecha.required = programar;
                publicar.textContent = programar ? '⏰ Programar publicación' : 'Publicar ahora';
                publicar.dataset.confirmar = programar
                    ? 'Se publicará solo en la fecha y hora elegidas. ¿Continuar?'
                    : 'Al publicarlo, lo verán sus destinatarios. ¿Continuar?';
            }

            radios.forEach(r => r.addEventListener('change', aplicar));
            aplicar();
        })();

        // ── Necesita revisión: muestra el revisor y cambia "Publicar" por "Enviar a revisión" ──
        (() => {
            const casilla = document.getElementById('necesita-revision');
            if (!casilla) return;

            const campo    = document.getElementById('campo-revisor');
            const revisor  = document.getElementById('revisor_id');
            const revision = document.getElementById('btn-revision');
            const publicar = document.getElementById('btn-publicar');

            function aplicar() {
                campo.hidden     = !casilla.checked;
                revision.hidden  = !casilla.checked;
                publicar.hidden  = casilla.checked;
                revisor.required = casilla.checked;
                if (!casilla.checked) revisor.value = '';
            }

            casilla.addEventListener('change', aplicar);
            aplicar();
        })();

        // ── Destinatarios: elegir, buscar personas y contar ──
        (() => {
            const form       = document.getElementById('form-comunicado');
            const panel      = document.getElementById('elegir-panel');
            const buscador   = document.getElementById('buscar-persona');
            const resultados = document.getElementById('resultados-personas');
            const elegidas   = document.getElementById('personas-elegidas');
            const total      = document.getElementById('total-destinatarios');
            const palabra    = document.getElementById('palabra-personas');
            const token      = form.querySelector('input[name=_token]').value;

            const para = () => form.querySelector('input[name=para]:checked')?.value ?? 'todos';
            const escapar = t => String(t).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            // Contador: cuántas personas lo recibirán
            let esperaConteo = null;
            function contar() {
                clearTimeout(esperaConteo);
                esperaConteo = setTimeout(async () => {
                    const datos = new FormData();
                    datos.append('para', para());
                    form.querySelectorAll('input[name="destinos[]"]').forEach(el => {
                        if (el.type !== 'checkbox' || el.checked) datos.append('destinos[]', el.value);
                    });
                    try {
                        const r = await fetch(@json(route('comunicados.admin.contar-destinatarios')), {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                            body: datos,
                        });
                        const { total: n, correo } = await r.json();
                        total.textContent = n;
                        const resumenCorreo = document.getElementById('resumen-correo');
                        if (resumenCorreo) resumenCorreo.textContent = correo || 'Elige destinatarios para saber a quién se manda.';
                        palabra.textContent = n === 1 ? 'persona' : 'personas';
                    } catch {
                        total.textContent = '?';
                    }
                }, 300);
            }

            // Mostrar u ocultar el panel de "Elegir"
            form.querySelectorAll('input[name=para]').forEach(r => r.addEventListener('change', () => {
                panel.hidden = para() !== 'elegir';
                contar();
            }));
            panel.addEventListener('change', e => { if (e.target.matches('input[type=checkbox]')) contar(); });

            // Buscar personas
            let esperaBusqueda = null;
            buscador.addEventListener('input', () => {
                clearTimeout(esperaBusqueda);
                const q = buscador.value.trim();
                if (q.length < 2) { resultados.classList.remove('show'); return; }

                esperaBusqueda = setTimeout(async () => {
                    const r = await fetch(@json(route('comunicados.admin.buscar-personas')) + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                    const personas = await r.json();
                    const yaElegidas = new Set([...elegidas.querySelectorAll('[data-id]')].map(c => c.dataset.id));

                    resultados.innerHTML = personas.length
                        ? personas.map(p => `
                            <button type="button" class="resultado" data-id="${p.id}" data-nombre="${escapar(p.nombre)}" ${yaElegidas.has(String(p.id)) ? 'disabled style="opacity:.45"' : ''}>
                                <strong>${escapar(p.nombre)}</strong>
                                <small>${escapar(p.detalle || '')}${yaElegidas.has(String(p.id)) ? ' · ya está' : ''}</small>
                            </button>`).join('')
                        : '<div class="resultado-vacio">No se encontró a nadie.</div>';
                    resultados.classList.add('show');
                }, 300);
            });

            // Agregar a la persona elegida
            resultados.addEventListener('click', e => {
                const boton = e.target.closest('.resultado');
                if (!boton || boton.disabled) return;

                const chip = document.createElement('span');
                chip.className = 'persona-chip';
                chip.dataset.id = boton.dataset.id;
                chip.innerHTML = `${escapar(boton.dataset.nombre)}
                    <input type="hidden" name="destinos[]" value="usuario:${boton.dataset.id}">
                    <button type="button" title="Quitar" data-quitar-persona>✕</button>`;
                elegidas.appendChild(chip);

                buscador.value = '';
                resultados.classList.remove('show');
                buscador.focus();
                contar();
            });

            // Quitar a una persona
            elegidas.addEventListener('click', e => {
                if (e.target.matches('[data-quitar-persona]')) {
                    e.target.closest('.persona-chip').remove();
                    contar();
                }
            });

            // Cerrar los resultados al hacer clic fuera, y que Enter no envíe el formulario
            document.addEventListener('click', e => { if (!e.target.closest('.buscador')) resultados.classList.remove('show'); });
            buscador.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });

            contar();
        })();

        // ── Plantilla de avisos de RH: vista previa en vivo y generación al guardar ──
        (() => {
            const plantilla = document.getElementById('plantilla-aviso');
            if (!plantilla || !window.htmlToImage) return;

            const form        = document.getElementById('form-comunicado');
            const casilla     = document.querySelector('input[type=checkbox][name=usar_plantilla]');
            const campoImg    = document.getElementById('imagen_generada');
            const inputPropia = document.getElementById('imagen-input');
            const preview     = document.getElementById('imagen-preview');
            const aviso       = document.getElementById('generando');
            const $ = id => document.getElementById(id);

            const escapar = t => t.replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            // El mismo formato que en el servidor: párrafos, saltos de línea y **negritas**
            const textoAHtml = t => t.trim().split(/\n\s*\n/).filter(p => p.trim())
                .map(p => '<p>' + escapar(p.trim()).replace(/\*\*(.+?)\*\*/gs, '<strong>$1</strong>').replace(/\n/g, '<br>') + '</p>')
                .join('');

            function pintar() {
                $('pa-encabezado').textContent = $('encabezado')?.value || 'AVISO IMPORTANTE';
                $('pa-titulo').textContent     = $('titulo').value;
                $('pa-cuerpo').innerHTML       = textoAHtml($('contenido').value);
                $('pa-firma').textContent      = $('firma').value || 'Recursos Humanos';
            }

            /*
             * La fuente Veneer, ya convertida a texto (data URL), para meterla dentro de la imagen.
             * Se la damos hecha a la librería para que no revise las demás hojas de estilo de la
             * página (las de Vite y las de fuentes externas), que el navegador no la deja leer.
             */
            let fuente = null;
            function prepararFuente() {
                fuente ??= fetch(@json(asset('fonts/Veneer.ttf')))
                    .then(r => r.ok ? r.blob() : Promise.reject(new Error('No se encontró la fuente Veneer')))
                    .then(blob => new Promise((ok, mal) => {
                        const lector = new FileReader();
                        lector.onload = () => ok(lector.result);
                        lector.onerror = mal;
                        lector.readAsDataURL(blob);
                    }))
                    .then(dataUrl => `@font-face { font-family: 'Veneer'; src: url(${dataUrl}) format('truetype'); }`)
                    .catch(error => {
                        console.warn(error);
                        return '';
                    });
                return fuente;
            }

            async function generar() {
                pintar();
                const css = await prepararFuente();
                await plantilla.querySelector('img')?.decode?.().catch(() => {});

                return htmlToImage.toBlob(plantilla, {
                    pixelRatio: 1,
                    backgroundColor: '#ffffff',
                    style: { position: 'static', left: '0', top: '0' },
                    ...(css ? { fontEmbedCSS: css } : { skipFonts: true }),
                });
            }

            let espera = null;
            function actualizarVista() {
                if (!casilla?.checked) return;
                clearTimeout(espera);
                espera = setTimeout(async () => {
                    aviso?.classList.add('show');
                    try {
                        const blob = await generar();
                        if (!blob) return;
                        preview.src = URL.createObjectURL(blob);
                        preview.style.display = 'block';
                        $('imagen-vacia')?.remove();
                    } catch (error) {
                        console.error('No se pudo generar la vista previa del aviso', error);
                    } finally {
                        aviso?.classList.remove('show');
                    }
                }, 700);
            }
            ['titulo', 'contenido', 'firma'].forEach(id => $(id)?.addEventListener('input', actualizarVista));
            $('encabezado')?.addEventListener('change', actualizarVista);
            casilla?.addEventListener('change', actualizarVista);
            actualizarVista();

            inputPropia?.addEventListener('change', () => { if (inputPropia.files.length && casilla) casilla.checked = false; });

            form.addEventListener('submit', async (e) => {
                if (!casilla?.checked || !$('contenido').value.trim() || form.dataset.imagenLista === '1') return;

                e.preventDefault();
                e.stopImmediatePropagation(); // antes que el bloqueo de doble envío

                try {
                    const blob = await generar();
                    const archivos = new DataTransfer();
                    archivos.items.add(new File([blob], 'aviso.png', { type: 'image/png' }));
                    campoImg.files = archivos.files;

                    form.dataset.imagenLista = '1';
                    form.submit();
                } catch (error) {
                    console.error(error);
                    alert('No se pudo generar la imagen del aviso. Intenta de nuevo, o desactiva la plantilla y sube una imagen.');
                    form.querySelectorAll('button[type=submit]').forEach(b => { b.disabled = false; });
                }
            }, true);
        })();
    </script>
</x-app-layout>