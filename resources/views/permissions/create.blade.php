<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('permissions.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Solicitar Permiso</h2>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:       #6A2C75;
            --brand-dark:  #541f5c;
            --brand-light: #f3eef5;
            --brand-mid:   #BBA4C0;
            --gold:        #D6A644;
            --gold-light:  #EED39B;
            --brown:       #473524;
            --rose:        #AA4969;
            --beige-light: #f7f3ef;
            --border:      #e6d9ed;
            --text:        #2c1a30;
            --muted:       #7a6682;
            --error:       #AA4969;
            --error-bg:    #fce8ee;
        }

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

        .field-input,.field-select,.field-textarea { padding:10px 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .field-input  { height:42px; }
        .field-select { height:42px; }
        .field-textarea { resize:vertical; min-height:90px; line-height:1.55; }
        .field-input:focus,.field-select:focus,.field-textarea:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-hint { font-size:12px; color:var(--muted); margin-top:3px; }

        /* Tipo entrada/salida como cards seleccionables */
        .tipo-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .tipo-card { display:flex; align-items:center; gap:10px; padding:12px 16px; border:1.5px solid var(--border); border-radius:10px; cursor:pointer; transition:all .15s; user-select:none; }
        .tipo-card input { display:none; }
        .tipo-card:hover { border-color:var(--brand-mid); background:var(--brand-light); }
        .tipo-card.selected { border-color:var(--brand); background:var(--brand-light); }
        .tipo-icon { width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .tipo-icon-in  { background:#e0eaff; color:#1d4ed8; }
        .tipo-icon-out { background:var(--gold-pale); color:var(--brown); }
        .tipo-name  { font-size:13px; font-weight:700; color:var(--text); }
        .tipo-desc  { font-size:11px; color:var(--muted); }

        /* Dropzone documento */
        .dropzone { border:2px dashed var(--border); border-radius:10px; padding:18px; text-align:center; cursor:pointer; transition:border-color .2s,background .2s; position:relative; }
        .dropzone:hover { border-color:var(--brand); background:var(--brand-light); }
        .dropzone input { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
        .dropzone-icon { font-size:26px; margin-bottom:5px; }
        .dropzone p { font-size:13px; color:var(--muted); margin:0; }
        .dropzone strong { color:var(--brand); }
        #preview { margin-top:12px; }
        #preview img { max-width:100%; border-radius:8px; border:1px solid var(--border); }
        #preview iframe { width:100%; height:280px; border-radius:8px; border:1px solid var(--border); }

        /* Aviso privacidad */
        .privacy-check { display:flex; align-items:flex-start; gap:10px; padding:12px 14px; background:var(--beige-light); border:1px solid var(--border); border-radius:9px; cursor:pointer; }
        .privacy-check input { display:none; }
        .custom-check { width:18px; height:18px; border-radius:5px; border:2px solid var(--border); flex-shrink:0; display:flex; align-items:center; justify-content:center; margin-top:1px; transition:all .15s; background:#fff; }
        .privacy-check.checked .custom-check { background:var(--brand); border-color:var(--brand); }
        .privacy-check .check-svg { display:none; }
        .privacy-check.checked .check-svg { display:block; }
        .privacy-text { font-size:13px; color:var(--text); }
        .privacy-text a { color:var(--brand); text-decoration:underline; }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }
        #submit-btn:disabled { opacity:.45; cursor:not-allowed; transform:none !important; box-shadow:none !important; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="form-card">
                <form action="{{ route('permissions.store') }}" method="POST" enctype="multipart/form-data" id="permForm">
                    @csrf

                    {{-- Información del solicitante (autocompletado) --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Información del Solicitante
                        </p>

                        {{-- Banner del empleado --}}
                        <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; background:var(--brand-light); border:1px solid var(--border); border-radius:10px; margin-bottom:18px;">
                            @if($authUser->foto_perfil)
                                <img src="{{ asset('storage/' . $authUser->foto_perfil) }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid var(--brand-mid);" alt="">
                            @else
                                <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--brand),var(--brand-mid));display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($authUser->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p style="font-weight:700;font-size:14px;color:var(--text);margin:0;">{{ $authUser->name }}</p>
                                <p style="font-size:12px;color:var(--muted);margin:0;">{{ $authUser->puesto_empleado ?? '' }} · {{ $authUser->departamento->name ?? '' }}</p>
                            </div>
                            <span style="margin-left:auto;font-size:11px;font-weight:600;background:#fff;border:1px solid var(--brand-mid);color:var(--brand);border-radius:6px;padding:2px 8px;">Autocompletado</span>
                        </div>

                        <div class="form-grid">
                            {{-- Puesto: oculto real + visible readonly --}}
                            <div class="field">
                                <label class="field-label">Puesto</label>
                                <input type="hidden" name="position" value="{{ $authUser->puesto_empleado ?? old('position') }}">
                                <input type="text" class="field-input" value="{{ $authUser->puesto_empleado ?? 'Sin puesto registrado' }}" readonly
                                    style="background:var(--beige-light);color:var(--muted);cursor:not-allowed;border-color:#ede3f2;">
                            </div>

                            {{-- Departamento: oculto real + visible readonly --}}
                            <div class="field">
                                <label class="field-label">Departamento</label>
                                <input type="hidden" name="department_id" value="{{ $authUser->departamento_id ?? old('department_id') }}">
                                <input type="text" class="field-input" value="{{ $authUser->departamento->name ?? 'Sin departamento registrado' }}" readonly
                                    style="background:var(--beige-light);color:var(--muted);cursor:not-allowed;border-color:#ede3f2;">
                            </div>
                        </div>
                    </div>

                    {{-- Tipo de permiso --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Tipo y Horario
                        </p>

                        {{-- Cards entrada/salida --}}
                        <div class="field" style="margin-bottom:18px;">
                            <label class="field-label">Tipo de Permiso <span class="req">*</span></label>
                            <div class="tipo-grid">
                                <label class="tipo-card {{ old('entry_exit_type') === 'entrada' ? 'selected' : '' }}" id="card-entrada" onclick="selectTipo('entrada')">
                                    <input type="radio" name="entry_exit_type" value="entrada" {{ old('entry_exit_type') === 'entrada' ? 'checked' : '' }}>
                                    <div class="tipo-icon tipo-icon-in">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                                    </div>
                                    <div>
                                        <p class="tipo-name">Entrada</p>
                                        <p class="tipo-desc">Llegada tardía</p>
                                    </div>
                                </label>
                                <label class="tipo-card {{ old('entry_exit_type') === 'salida' ? 'selected' : '' }}" id="card-salida" onclick="selectTipo('salida')">
                                    <input type="radio" name="entry_exit_type" value="salida" {{ old('entry_exit_type') === 'salida' ? 'checked' : '' }}>
                                    <div class="tipo-icon tipo-icon-out">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </div>
                                    <div>
                                        <p class="tipo-name">Salida</p>
                                        <p class="tipo-desc">Salida anticipada</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label" for="official_schedule">Horario Oficial <span class="req">*</span></label>
                                <input type="time" name="official_schedule" id="official_schedule" class="field-input" required>
                                <span class="field-hint">Tu horario de trabajo habitual</span>
                            </div>
                            <div class="field">
                                <label class="field-label" for="entry_exit_time">Hora de Entrada/Salida <span class="req">*</span></label>
                                <input type="time" name="entry_exit_time" id="entry_exit_time" class="field-input" required>
                                <span class="field-hint">La hora real del permiso</span>
                            </div>
                            <div class="col-span-2 field">
                                <label class="field-label" for="date">Fecha del Permiso <span class="req">*</span></label>
                                <input type="date" name="date" id="date" class="field-input" required>
                            </div>
                        </div>
                    </div>

                    {{-- Motivo --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            Motivo del Permiso
                        </p>
                        <div class="field">
                            <label class="field-label" for="reason">Descripción <span class="req">*</span></label>
                            <textarea name="reason" id="reason" class="field-textarea"
                                placeholder="Describe el motivo de forma clara y concisa..." required maxlength="500">{{ old('reason') }}</textarea>
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                                <span class="field-hint">Sé claro y específico con el motivo.</span>
                                <span id="char-count" style="font-size:12px; color:var(--muted);">0 / 500</span>
                            </div>
                            @error('reason')<span style="font-size:12px;color:var(--error);">⚠ {{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Documento de soporte --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            Documento de Soporte
                            <span class="opt" style="margin-left:4px;">Opcional</span>
                        </p>
                        <div class="dropzone" id="dropzone">
                            <input type="file" name="supporting_document" id="supporting_document" accept="image/*,.pdf" onchange="previewDoc(event)">
                            <div id="dz-content">
                                <div class="dropzone-icon">📎</div>
                                <p><strong>Haz clic para adjuntar</strong> o arrastra aquí</p>
                                <p style="margin-top:4px; font-size:12px;">Imagen o PDF</p>
                            </div>
                        </div>
                        <div id="preview"></div>
                    </div>

                    {{-- Aviso de privacidad --}}
                    <div class="form-section">
                        <label class="privacy-check" id="privacy-wrap" onclick="togglePrivacy()">
                            <input type="checkbox" name="aviso_privacidad" id="aviso_privacidad" required>
                            <div class="custom-check">
                                <svg class="w-3 h-3 check-svg" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="privacy-text">
                                He leído y acepto el
                                <a href="https://dasavena.com/pages/terminos-condiciones/#aviso-privacidad" target="_blank" onclick="event.stopPropagation()">Aviso de Privacidad</a>.
                            </span>
                        </label>
                    </div>

                    {{-- Footer --}}
                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('permissions.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" id="submit-btn" class="btn btn-primary" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enviar Solicitud
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fecha mínima = ayer
        window.addEventListener('DOMContentLoaded', () => {
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            document.getElementById('date').min = yesterday.toISOString().split('T')[0];
        });

        // Tipo entrada/salida
        function selectTipo(tipo) {
            document.getElementById('card-entrada').classList.toggle('selected', tipo === 'entrada');
            document.getElementById('card-salida').classList.toggle('selected', tipo === 'salida');
            document.querySelector(`input[value="${tipo}"]`).checked = true;
        }

        // Contador de caracteres
        const reason = document.getElementById('reason');
        const counter = document.getElementById('char-count');
        reason.addEventListener('input', () => {
            const n = reason.value.length;
            counter.textContent = n + ' / 500';
            counter.style.color = n > 450 ? 'var(--rose)' : 'var(--muted)';
        });

        // Aviso privacidad
        function togglePrivacy() {
            const wrap = document.getElementById('privacy-wrap');
            const cb   = document.getElementById('aviso_privacidad');
            cb.checked = !cb.checked;
            wrap.classList.toggle('checked', cb.checked);
            document.getElementById('submit-btn').disabled = !cb.checked;
        }

        // Preview documento
        function previewDoc(e) {
            const file = e.target.files[0];
            const prev = document.getElementById('preview');
            prev.innerHTML = '';
            document.getElementById('dz-content').style.display = 'none';
            if (!file) return;
            if (file.type.startsWith('image/')) {
                prev.innerHTML = `<img src="${URL.createObjectURL(file)}" style="max-width:100%;border-radius:8px;border:1px solid var(--border);margin-top:10px;">`;
            } else if (file.type === 'application/pdf') {
                prev.innerHTML = `<iframe src="${URL.createObjectURL(file)}" style="width:100%;height:280px;border-radius:8px;border:1px solid var(--border);margin-top:10px;"></iframe>`;
            }
        }
    </script>
</x-app-layout>