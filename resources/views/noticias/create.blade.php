<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('noticias.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Crear Noticia</h2>
        </div>
    </x-slot>

    <link rel="stylesheet" href="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js">

    <style>
        :root { --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0; --gold:#D6A644; --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682; --error:#AA4969; }
        .form-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }
        .form-section { padding:26px 32px; border-bottom:1px solid #f0e6f5; }
        .form-section-footer { padding:20px 32px; background:var(--beige-light); border-top:1.5px solid var(--border); }
        .section-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); margin-bottom:18px; display:flex; align-items:center; gap:8px; }
        .section-title::after { content:''; flex:1; height:1px; background:linear-gradient(to right,#e0c8e8,transparent); }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        @media(max-width:600px){ .form-grid{grid-template-columns:1fr;} }
        .col-span-2 { grid-column:span 2; }
        @media(max-width:600px){ .col-span-2{grid-column:span 1;} }
        .field { display:flex; flex-direction:column; gap:5px; }
        .field-label { font-size:13px; font-weight:600; color:var(--brown); display:flex; align-items:center; gap:5px; }
        .req { color:var(--rose); }
        .opt { font-size:11px; font-weight:400; color:var(--muted); background:#f0e6f5; border-radius:4px; padding:1px 7px; }
        .field-input,.field-textarea { padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .field-input { height:42px; }
        .field-textarea { padding:12px 14px; resize:vertical; min-height:100px; }
        .field-input:focus,.field-textarea:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-error { font-size:12px; color:var(--error); }
        .field-hint  { font-size:12px; color:var(--muted); }

        .dropzone { border:2px dashed var(--border); border-radius:10px; padding:20px; text-align:center; cursor:pointer; transition:border-color .2s,background .2s; position:relative; }
        .dropzone:hover { border-color:var(--brand); background:var(--brand-light); }
        .dropzone input { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
        .dz-icon { font-size:26px; margin-bottom:6px; }
        .dropzone p { font-size:13px; color:var(--muted); margin:0; }
        .dropzone strong { color:var(--brand); }

        .preview-thumb { width:100%; height:160px; object-fit:cover; border-radius:9px; border:1px solid var(--border); margin-top:10px; display:none; }

        /* Galería actual */
        .gallery-grid { display:flex; flex-wrap:wrap; gap:8px; margin-top:8px; }
        .gallery-thumb { width:80px; height:80px; object-fit:cover; border-radius:7px; border:2px solid var(--border); }

        /* Imagen actual noticia */
        .current-img { width:100%; height:140px; object-fit:cover; border-radius:9px; border:1px solid var(--border); margin-bottom:10px; }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }

        .error-box { background:#fce8ee; border:1px solid #f0b3c3; border-radius:10px; padding:14px 16px; margin-bottom:16px; }
        .error-box p { font-size:13px; font-weight:700; color:var(--rose); margin-bottom:6px; }
        .error-box li { font-size:13px; color:#7a2039; margin-left:14px; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="error-box">
                    <p>Por favor corrige los siguientes errores:</p>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-card">
                <form action="{{ route('noticias.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="activo" value="1">

                    {{-- Información básica --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Información de la Noticia
                        </p>
                        <div class="form-grid">
                            <div class="col-span-2 field">
                                <label class="field-label" for="titulo">Título <span class="req">*</span></label>
                                <input type="text" id="titulo" name="titulo" class="field-input" value="{{ old('titulo') }}" placeholder="Título de la noticia" required>
                                @error('titulo')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="autor">Autor <span class="req">*</span></label>
                                <input type="text" id="autor" name="autor" class="field-input" value="{{ old('autor') }}" placeholder="Nombre del autor" required>
                                @error('autor')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="fecha">Fecha <span class="req">*</span></label>
                                <input type="date" id="fecha" name="fecha" class="field-input" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                @error('fecha')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="col-span-2 field">
                                <label class="field-label" for="multimedia">
                                    Video / Multimedia
                                    <span class="opt">Opcional</span>
                                </label>
                                <input type="url" id="multimedia" name="multimedia" class="field-input" value="{{ old('multimedia') }}" placeholder="https://youtube.com/...">
                                <span class="field-hint">URL de YouTube u otro servicio de video.</span>
                                @error('multimedia')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Contenido --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            Contenido
                        </p>
                        <div class="field">
                            <label class="field-label" for="contenido">Cuerpo de la noticia <span class="req">*</span></label>
                            <textarea id="contenido" name="contenido" rows="6" class="field-textarea" required>{{ old('contenido') }}</textarea>
                            @error('contenido')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Imágenes --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Imágenes
                        </p>
                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label">Imagen de Portada <span class="req">*</span></label>
                                <div class="dropzone">
                                    <input type="file" name="imagen" accept="image/*" required onchange="previewCover(event)">
                                    <div id="cover-dz">
                                        <div class="dz-icon">📸</div>
                                        <p><strong>Subir portada</strong></p>
                                        <p style="font-size:12px;margin-top:3px;">PNG, JPG · Recomendado 800×500px</p>
                                    </div>
                                </div>
                                <img id="cover-preview" class="preview-thumb" src="" alt="">
                                @error('imagen')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label">Galería <span class="opt">Opcional</span></label>
                                <div class="dropzone">
                                    <input type="file" name="galeria[]" accept="image/*" multiple>
                                    <div>
                                        <div class="dz-icon">🖼️</div>
                                        <p><strong>Subir imágenes</strong></p>
                                        <p style="font-size:12px;margin-top:3px;">Puedes seleccionar múltiples</p>
                                    </div>
                                </div>
                                @error('galeria')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('noticias.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Publicar Noticia
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => CKEDITOR.replace('contenido'));
        function previewCover(e) {
            const file = e.target.files[0];
            if (!file) return;
            const img = document.getElementById('cover-preview');
            img.src = URL.createObjectURL(file);
            img.style.display = 'block';
            document.getElementById('cover-dz').style.display = 'none';
        }
    </script>
</x-app-layout>