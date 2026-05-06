<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Administrar Carrusel
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ count($carruselImages) }} imagen(es)
            </span>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5;
            --brand-mid:#BBA4C0; --gold:#D6A644; --gold-pale:#faf3e0;
            --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef;
            --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682;
        }
        .card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }

        /* Grid de imágenes */
        .carousel-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:20px; padding:24px; }

        .img-card { position:relative; border-radius:12px; overflow:hidden; border:1px solid var(--border); box-shadow:0 2px 8px rgba(106,44,117,.07); transition:transform .2s, box-shadow .2s; }
        .img-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(106,44,117,.15); }

        .img-card img { width:100%; height:180px; object-fit:cover; display:block; }

        .img-card-overlay {
            position:absolute; inset:0;
            background:linear-gradient(to top, rgba(42,10,50,.75) 0%, transparent 55%);
            opacity:0; transition:opacity .2s;
            display:flex; flex-direction:column; justify-content:flex-end; padding:14px;
        }
        .img-card:hover .img-card-overlay { opacity:1; }

        .img-card-title { font-size:13px; font-weight:700; color:#fff; line-height:1.3; margin-bottom:3px; }
        .img-card-desc  { font-size:11px; color:rgba(255,255,255,.75); }

        /* Botones de acción flotantes */
        .img-actions { position:absolute; top:10px; right:10px; display:flex; gap:6px; }
        .img-action-btn {
            width:32px; height:32px; border-radius:8px;
            display:flex; align-items:center; justify-content:center;
            border:none; cursor:pointer; font-size:14px;
            backdrop-filter:blur(8px); transition:transform .15s;
        }
        .img-action-btn:hover { transform:scale(1.12); }
        .btn-edit   { background:rgba(214,166,68,.85); }
        .btn-delete { background:rgba(170,73,105,.85); }

        /* Info card footer */
        .img-info { padding:10px 14px; background:var(--beige-light); border-top:1px solid var(--border); }
        .img-info-title { font-size:13px; font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .img-info-desc  { font-size:11px; color:var(--muted); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

        /* Modal edición */
        .edit-modal { position:fixed; inset:0; z-index:50; display:flex; align-items:center; justify-content:center; background:rgba(42,10,50,.55); backdrop-filter:blur(4px); padding:20px; opacity:0; pointer-events:none; transition:opacity .2s; }
        .edit-modal.show { opacity:1; pointer-events:auto; }
        .edit-modal-box { background:#fff; border-radius:16px; width:100%; max-width:480px; box-shadow:0 24px 60px rgba(0,0,0,.2); overflow:hidden; transform:translateY(12px); transition:transform .2s; }
        .edit-modal.show .edit-modal-box { transform:translateY(0); }

        .modal-header { padding:18px 24px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
        .modal-header h3 { font-size:16px; font-weight:700; color:var(--text); }
        .modal-close { background:none; border:none; font-size:18px; color:var(--muted); cursor:pointer; }

        .modal-body { padding:24px; }
        .modal-footer { padding:16px 24px; background:var(--beige-light); border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px; }

        .field { display:flex; flex-direction:column; gap:5px; margin-bottom:16px; }
        .field-label { font-size:13px; font-weight:600; color:var(--brown); }
        .field-input,.field-textarea { padding:10px 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .field-input:focus,.field-textarea:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-textarea { resize:vertical; min-height:80px; }

        .preview-img { width:100%; height:140px; object-fit:cover; border-radius:9px; margin-bottom:14px; border:1px solid var(--border); }

        .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:40px; border-radius:9px; font-size:13.5px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }

        .alert-success { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; display:flex; align-items:center; gap:10px; background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }

        .empty-state { text-align:center; padding:56px 24px; color:var(--muted); }
        .empty-state svg { width:52px; height:52px; margin:0 auto 14px; opacity:.3; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert-success">
                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">

                {{-- Header --}}
                <div style="padding:16px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border);">
                    <p style="font-size:13px; color:var(--muted);">Gestiona las imágenes que aparecen en el carrusel del dashboard.</p>
                    <a href="{{ route('carrusel.create') }}" class="btn btn-primary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Subir Imagen
                    </a>
                </div>

                {{-- Grid --}}
                @if($carruselImages->isEmpty())
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p style="font-size:15px; font-weight:600; color:var(--text); margin-bottom:4px;">Sin imágenes</p>
                        <p style="font-size:13px;">Aún no has subido imágenes al carrusel.</p>
                    </div>
                @else
                    <div class="carousel-grid">
                        @foreach($carruselImages as $image)
                            <div class="img-card">
                                <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->titulo }}">

                                <div class="img-card-overlay">
                                    @if($image->titulo)
                                        <p class="img-card-title">{{ $image->titulo }}</p>
                                    @endif
                                    @if($image->descripcion)
                                        <p class="img-card-desc">{{ Str::limit($image->descripcion, 60) }}</p>
                                    @endif
                                </div>

                                <div class="img-actions">
                                    <button class="img-action-btn btn-edit" title="Editar"
                                        onclick="openEditModal({{ $image->id }}, '{{ addslashes($image->titulo) }}', '{{ addslashes($image->descripcion) }}', '{{ Storage::url($image->image) }}')">
                                        ✏️
                                    </button>
                                    <button class="img-action-btn btn-delete" title="Eliminar"
                                        onclick="deleteImage({{ $image->id }})">
                                        🗑️
                                    </button>
                                </div>

                                <div class="img-info">
                                    <p class="img-info-title">{{ $image->titulo ?: 'Sin título' }}</p>
                                    <p class="img-info-desc">{{ $image->descripcion ?: 'Sin descripción' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Modal edición --}}
    <div class="edit-modal" id="editModal" onclick="handleModalBg(event)">
        <div class="edit-modal-box">
            <div class="modal-header">
                <h3>Editar Imagen</h3>
                <button class="modal-close" onclick="closeEditModal()">✕</button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editImageId" name="id">
                <div class="modal-body">
                    <img id="editImagePreview" src="" alt="" class="preview-img">
                    <div class="field">
                        <label class="field-label">Cambiar imagen <span style="font-size:11px;color:var(--muted);">(Opcional)</span></label>
                        <input type="file" id="editImage" name="image" accept="image/*" class="field-input" style="padding:8px 14px;" onchange="previewEdit(event)">
                    </div>
                    <div class="field">
                        <label class="field-label" for="editTitulo">Título</label>
                        <input type="text" id="editTitulo" name="titulo" class="field-input" placeholder="Título del slide">
                    </div>
                    <div class="field">
                        <label class="field-label" for="editDescripcion">Descripción</label>
                        <textarea id="editDescripcion" name="descripcion" class="field-textarea" placeholder="Descripción breve"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, titulo, descripcion, imageUrl) {
            document.getElementById('editImageId').value    = id;
            document.getElementById('editTitulo').value     = titulo;
            document.getElementById('editDescripcion').value = descripcion;
            document.getElementById('editImagePreview').src  = imageUrl;
            document.getElementById('editForm').action       = `/carrusel/${id}`;
            document.getElementById('editModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
            document.body.style.overflow = '';
        }
        function handleModalBg(e) {
            if (e.target === e.currentTarget) closeEditModal();
        }
        function previewEdit(e) {
            const file = e.target.files[0];
            if (file) document.getElementById('editImagePreview').src = URL.createObjectURL(file);
        }
        function deleteImage(id) {
            if (!confirm('¿Seguro que deseas eliminar esta imagen del carrusel?')) return;
            fetch(`/carrusel/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json())
            .then(d => { if (d.success) location.reload(); else alert('Error al eliminar la imagen'); })
            .catch(e => console.error(e));
        }
    </script>
</x-app-layout>