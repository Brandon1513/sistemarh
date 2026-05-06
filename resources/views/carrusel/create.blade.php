<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('carrusel.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Nueva Imagen para el Carrusel</h2>
        </div>
    </x-slot>

    <style>
        :root { --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0; --gold:#D6A644; --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682; --error:#AA4969; }
        .form-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }
        .form-section { padding:26px 32px; border-bottom:1px solid #f0e6f5; }
        .form-section-footer { padding:20px 32px; background:var(--beige-light); border-top:1.5px solid var(--border); }
        .section-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); margin-bottom:18px; display:flex; align-items:center; gap:8px; }
        .section-title::after { content:''; flex:1; height:1px; background:linear-gradient(to right,#e0c8e8,transparent); }
        .field { display:flex; flex-direction:column; gap:5px; margin-bottom:18px; }
        .field-label { font-size:13px; font-weight:600; color:var(--brown); display:flex; align-items:center; gap:5px; }
        .req { color:var(--rose); }
        .opt { font-size:11px; font-weight:400; color:var(--muted); background:#f0e6f5; border-radius:4px; padding:1px 7px; }
        .field-input,.field-textarea { padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .field-input { height:42px; }
        .field-textarea { padding:12px 14px; resize:vertical; min-height:90px; }
        .field-input:focus,.field-textarea:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-error { font-size:12px; color:var(--error); }

        .dropzone { border:2px dashed var(--border); border-radius:10px; padding:28px; text-align:center; cursor:pointer; transition:border-color .2s,background .2s; position:relative; }
        .dropzone:hover { border-color:var(--brand); background:var(--brand-light); }
        .dropzone input { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
        .dropzone-icon { font-size:32px; margin-bottom:8px; }
        .dropzone p { font-size:13px; color:var(--muted); margin:0; }
        .dropzone strong { color:var(--brand); }

        #preview-wrap { display:none; margin-top:12px; }
        #preview-img { width:100%; height:200px; object-fit:cover; border-radius:10px; border:1px solid var(--border); }
        #preview-name { font-size:12px; color:var(--muted); margin-top:6px; }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="form-card">
                <form method="POST" action="{{ route('carrusel.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Imagen
                        </p>

                        <div class="field">
                            <label class="field-label">Imagen del Slide <span class="req">*</span></label>
                            <div class="dropzone" id="dropzone">
                                <input type="file" id="image" name="image" accept="image/*" required onchange="previewImg(event)">
                                <div id="dz-content">
                                    <div class="dropzone-icon">🖼️</div>
                                    <p><strong>Haz clic para subir</strong> o arrastra aquí</p>
                                    <p style="margin-top:4px; font-size:12px;">PNG, JPG, WEBP · Recomendado 1920×600px</p>
                                </div>
                            </div>
                            <div id="preview-wrap">
                                <img id="preview-img" src="" alt="Preview">
                                <p id="preview-name"></p>
                            </div>
                            @error('image')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            Texto del Slide
                        </p>

                        <div class="field">
                            <label class="field-label" for="titulo">Título <span class="opt">Opcional</span></label>
                            <input type="text" id="titulo" name="titulo" class="field-input" value="{{ old('titulo') }}" placeholder="Ej. Bienvenidos a nuestro equipo">
                            @error('titulo')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>

                        <div class="field" style="margin-bottom:0;">
                            <label class="field-label" for="descripcion">Descripción <span class="opt">Opcional</span></label>
                            <textarea id="descripcion" name="descripcion" class="field-textarea" placeholder="Descripción breve que aparecerá debajo del título">{{ old('descripcion') }}</textarea>
                            @error('descripcion')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('carrusel.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Subir Imagen
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImg(e) {
            const file = e.target.files[0];
            if (!file) return;
            document.getElementById('preview-img').src  = URL.createObjectURL(file);
            document.getElementById('preview-name').textContent = '📎 ' + file.name;
            document.getElementById('preview-wrap').style.display = 'block';
            document.getElementById('dz-content').style.display  = 'none';
        }
    </script>
</x-app-layout>