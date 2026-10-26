@php
    use App\Models\Comunicado;

    $editando = $comunicado->exists;
    $tipo = old('tipo', $comunicado->tipo);
    $iconosTipo = ['noticia' => '📰', 'red_social' => '📱', 'aviso' => '📢'];
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
        .layout { display:grid; grid-template-columns:minmax(0,1fr) 300px; gap:20px; align-items:start; }
        @media(max-width:960px){ .layout { grid-template-columns:minmax(0,1fr); } }
        .card { background:#fff; border-radius:16px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); }
        .card-body { padding:26px 28px; display:flex; flex-direction:column; gap:20px; }
        .card-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); }
        .field { display:flex; flex-direction:column; gap:5px; }
        .label { font-size:13px; font-weight:600; color:var(--brown); }
        .req { color:var(--rose); }
        .opt { font-size:11px; font-weight:400; color:var(--muted); background:#f0e6f5; border-radius:4px; padding:1px 7px; margin-left:4px; }
        .input, .select, .textarea { width:100%; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; padding:0 14px; }
        .input, .select { height:42px; }
        .textarea { padding:12px 14px; line-height:1.6; resize:vertical; }
        .input:focus, .select:focus, .textarea:focus { border-color:var(--brand); outline:none; box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .titulo-input { height:52px; font-size:19px; font-weight:700; }
        .hint { font-size:12px; color:var(--muted); }
        .error { font-size:12px; color:var(--rose); }
        .contador { font-size:11.5px; color:var(--muted); text-align:right; }

        .imagen-zona { position:relative; border:2px dashed var(--border); border-radius:12px; overflow:hidden; background:var(--beige-light); min-height:170px; display:flex; align-items:center; justify-content:center; text-align:center; cursor:pointer; }
        .imagen-zona:hover { border-color:var(--brand); }
        .imagen-zona input { position:absolute; inset:0; opacity:0; cursor:pointer; }
        .imagen-zona img { width:100%; height:200px; object-fit:cover; }
        .imagen-zona .vacia { padding:20px; color:var(--muted); font-size:13px; }

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

        .btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; padding:0 20px; height:44px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; width:100%; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); }
        .btn-outline { background:#fff; color:var(--brand); border:1.5px solid var(--brand-mid); }
        .btn-outline:hover { border-color:var(--brand); }
        .alert { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; }
        .alert-success { background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }
        .alert-error { background:#fce8ee; color:#7a2039; border:1px solid #f0b3c3; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Revisa lo siguiente:</strong>
                    <ul style="margin:6px 0 0 18px; list-style:disc;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" enctype="multipart/form-data"
                  action="{{ $editando ? route('comunicados.admin.update', $comunicado) : route('comunicados.admin.store') }}">
                @csrf
                @if($editando) @method('PUT') @endif
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                <input type="hidden" name="accion" id="accion" value="borrador">
                <div class="layout">
                    {{-- ── Contenido ── --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="field">
                                <label class="label" for="titulo">Título <span class="req">*</span></label>
                                <input id="titulo" name="titulo" class="input titulo-input" maxlength="255" required
                                       value="{{ old('titulo', $comunicado->titulo) }}"
                                       placeholder="{{ $tipo === 'aviso' ? 'Ej. El pago de aguinaldo será el 15 de diciembre' : 'Ej. Lanzamos nuestra nueva línea de granolas' }}">
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

                    {{-- ── Panel lateral ── --}}
                    <div style="display:flex; flex-direction:column; gap:20px;">
                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Publicación</p>
                                <button type="submit" class="btn btn-primary"
                                        onclick="if (!confirm('Al publicarlo, lo verán todos los empleados. ¿Continuar?')) return false; document.getElementById('accion').value = 'publicar';">
                                    {{ $comunicado->estado === 'publicado' ? 'Guardar cambios' : 'Publicar ahora' }}
                                </button>
                                @if($comunicado->estado !== 'publicado')
                                    <button type="submit" class="btn btn-outline" formnovalidate
                                            onclick="document.getElementById('accion').value = 'borrador';">
                                        Guardar borrador
                                    </button>
                                @endif
                                <span class="hint">La programación por fecha y la revisión llegan en la siguiente fase.</span>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <p class="card-title">Imagen principal</p>
                                <label class="imagen-zona" id="imagen-zona">
                                    @if($comunicado->imagen_url)
                                        <img src="{{ $comunicado->imagen_url }}" id="imagen-preview" alt="">
                                    @else
                                        <span class="vacia" id="imagen-vacia">📷<br>Toca para elegir una imagen<br><small>JPG, PNG o WEBP de hasta 2 MB</small></span>
                                        <img id="imagen-preview" alt="" style="display:none;">
                                    @endif
                                    <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" id="imagen-input">
                                </label>
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

    <script>
        // Contadores de caracteres
        document.querySelectorAll('[data-contador]').forEach(el => {
            const salida = document.getElementById(el.dataset.contador);
            const pintar = () => { salida.textContent = `${el.value.length} / ${el.maxLength}`; };
            el.addEventListener('input', pintar);
            pintar();
        });

        // Vista previa de la imagen principal
        document.getElementById('imagen-input').addEventListener('change', e => {
            const archivo = e.target.files[0];
            if (!archivo) return;
            const img = document.getElementById('imagen-preview');
            img.src = URL.createObjectURL(archivo);
            img.style.display = 'block';
            document.getElementById('imagen-vacia')?.remove();
        });

        // Vista previa de las fotos nuevas de la galería
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
    </script>
</x-app-layout>