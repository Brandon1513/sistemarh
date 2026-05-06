<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('permisos.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Nueva Solicitud de Permiso</h2>
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
            --gold-pale:   #faf3e0;
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

        .field-input,.field-select,.field-textarea { padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .field-input  { height:42px; }
        .field-select { height:42px; }
        .field-textarea { padding:12px 14px; resize:vertical; min-height:90px; line-height:1.55; }
        .field-input:focus,.field-select:focus,.field-textarea:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-input[readonly],.field-input[disabled] { background:var(--beige-light); color:var(--muted); cursor:not-allowed; border-color:#ede3f2; }
        .field-hint { font-size:12px; color:var(--muted); }
        .field-error { font-size:12px; color:var(--error); }

        /* Tipo de solicitud: cards visuales */
        .tipo-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
        @media(max-width:500px){ .tipo-grid{grid-template-columns:1fr;} }
        .tipo-card { display:flex; flex-direction:column; align-items:center; gap:6px; padding:14px 10px; border:1.5px solid var(--border); border-radius:10px; cursor:pointer; transition:all .15s; user-select:none; text-align:center; }
        .tipo-card input { display:none; }
        .tipo-card:hover { border-color:var(--brand-mid); background:var(--brand-light); }
        .tipo-card.selected-permiso    { border-color:var(--brand); background:var(--brand-light); }
        .tipo-card.selected-comision   { border-color:var(--gold); background:var(--gold-pale); }
        .tipo-card.selected-suspension { border-color:var(--rose); background:#fce8ee; }
        .tipo-icon { width:38px; height:38px; border-radius:9px; display:flex; align-items:center; justify-content:center; }
        .icon-permiso    { background:var(--brand-light); color:var(--brand); }
        .icon-comision   { background:var(--gold-pale); color:var(--brown); }
        .icon-suspension { background:#fce8ee; color:var(--rose); }
        .tipo-name { font-size:13px; font-weight:700; color:var(--text); }

        /* Goce: radio pills */
        .goce-grid { display:flex; gap:10px; }
        .goce-pill { display:flex; align-items:center; gap:7px; padding:8px 16px; border:1.5px solid var(--border); border-radius:8px; cursor:pointer; transition:all .15s; user-select:none; flex:1; justify-content:center; }
        .goce-pill input { display:none; }
        .goce-pill:hover { border-color:var(--brand-mid); }
        .goce-pill.selected-con  { border-color:#2d7a4f; background:#e8f5ee; }
        .goce-pill.selected-sin  { border-color:var(--gold); background:var(--gold-pale); }
        .goce-pill span { font-size:13px; font-weight:600; color:var(--text); }

        /* Dropzone */
        .dropzone { border:2px dashed var(--border); border-radius:10px; padding:18px; text-align:center; cursor:pointer; transition:border-color .2s,background .2s; position:relative; }
        .dropzone:hover { border-color:var(--brand); background:var(--brand-light); }
        .dropzone input { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
        .dropzone-icon { font-size:26px; margin-bottom:5px; }
        .dropzone p { font-size:13px; color:var(--muted); margin:0; }
        .dropzone strong { color:var(--brand); }
        #preview { margin-top:12px; }
        #preview img { max-width:100%; border-radius:8px; border:1px solid var(--border); }
        #preview iframe { width:100%; height:280px; border-radius:8px; border:1px solid var(--border); }

        /* Chars */
        #char-count { font-size:12px; color:var(--muted); }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }

        /* Error list */
        .error-list { background:#fce8ee; border:1px solid #f0b3c3; border-radius:10px; padding:14px 16px; margin-top:16px; }
        .error-list p { font-size:13px; font-weight:700; color:var(--rose); margin-bottom:8px; }
        .error-list li { font-size:13px; color:#7a2039; margin-left:12px; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="form-card">
                <form method="POST" action="{{ route('permisos.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Solicitante (autocompletado) --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Solicitante
                        </p>

                        {{-- Banner empleado --}}
                        <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; background:var(--brand-light); border:1px solid var(--border); border-radius:10px; margin-bottom:16px;">
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

                        {{-- Campo oculto para departamento --}}
                        <input type="hidden" name="departamento_id" value="{{ $authUser->departamento_id }}">
                    </div>

                    {{-- Fechas --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Período del Permiso
                        </p>
                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label" for="fecha_inicio">Fecha de Inicio <span class="req">*</span></label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="field-input" value="{{ old('fecha_inicio') }}" required>
                                @error('fecha_inicio')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="fecha_termino">Fecha de Término <span class="req">*</span></label>
                                <input type="date" name="fecha_termino" id="fecha_termino" class="field-input" value="{{ old('fecha_termino') }}" required>
                                @error('fecha_termino')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="col-span-2 field">
                                <label class="field-label" for="fecha_regreso_laborar">
                                    Fecha de Regreso a Laborar
                                    <span class="opt">Opcional</span>
                                </label>
                                <input type="date" name="fecha_regreso_laborar" id="fecha_regreso_laborar" class="field-input" value="{{ old('fecha_regreso_laborar') }}">
                                <span class="field-hint">Si es diferente al día siguiente del término.</span>
                            </div>
                        </div>

                        {{-- Días calculados automáticamente --}}
                        <div id="dias-badge" style="display:none; margin-top:14px; padding:10px 14px; background:var(--brand-light); border:1px solid var(--border); border-radius:9px;">
                            <p style="font-size:13px; color:var(--brand); font-weight:600; margin:0;">
                                📅 Duración: <strong id="dias-count">—</strong> día(s) hábiles
                            </p>
                        </div>
                    </div>

                    {{-- Tipo de solicitud --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            Clasificación
                        </p>

                        {{-- Tipo de solicitud --}}
                        <div class="field" style="margin-bottom:18px;">
                            <label class="field-label">Tipo de Solicitud <span class="req">*</span></label>
                            <div class="tipo-grid">
                                @foreach([
                                    ['value'=>'Permiso',    'icon'=>'📋', 'class'=>'icon-permiso',    'sel'=>'selected-permiso'],
                                    ['value'=>'Comisión',   'icon'=>'🚗', 'class'=>'icon-comision',   'sel'=>'selected-comision'],
                                    ['value'=>'Suspensión', 'icon'=>'⛔', 'class'=>'icon-suspension', 'sel'=>'selected-suspension'],
                                ] as $t)
                                    <label class="tipo-card {{ old('tipo') === $t['value'] ? $t['sel'] : '' }}"
                                        id="tipo-card-{{ Str::slug($t['value']) }}"
                                        onclick="selectTipo('{{ $t['value'] }}', '{{ $t['sel'] }}')">
                                        <input type="radio" name="tipo" value="{{ $t['value'] }}" {{ old('tipo') === $t['value'] ? 'checked' : '' }}>
                                        <div class="tipo-icon {{ $t['class'] }}">{{ $t['icon'] }}</div>
                                        <span class="tipo-name">{{ $t['value'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('tipo')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>

                        {{-- Goce de sueldo --}}
                        <div class="field" style="margin-bottom:18px;">
                            <label class="field-label">Tipo de Permiso <span class="req">*</span></label>
                            <div class="goce-grid">
                                <label class="goce-pill {{ old('tipo_permiso') === 'Con Goce de Sueldo' ? 'selected-con' : '' }}" id="goce-con" onclick="selectGoce('Con Goce de Sueldo')">
                                    <input type="radio" name="tipo_permiso" value="Con Goce de Sueldo" {{ old('tipo_permiso') === 'Con Goce de Sueldo' ? 'checked' : '' }}>
                                    <span>✅ Con Goce de Sueldo</span>
                                </label>
                                <label class="goce-pill {{ old('tipo_permiso') === 'Sin Goce de Sueldo' ? 'selected-sin' : '' }}" id="goce-sin" onclick="selectGoce('Sin Goce de Sueldo')">
                                    <input type="radio" name="tipo_permiso" value="Sin Goce de Sueldo" {{ old('tipo_permiso') === 'Sin Goce de Sueldo' ? 'checked' : '' }}>
                                    <span>⚠️ Sin Goce de Sueldo</span>
                                </label>
                            </div>
                            @error('tipo_permiso')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>

                        {{-- Día de descanso --}}
                        <div class="field">
                            <label class="field-label" for="dia_descanso">
                                Día de Descanso
                                <span class="opt">Opcional</span>
                            </label>
                            <select name="dia_descanso" id="dia_descanso" class="field-select">
                                <option value="">No aplica</option>
                                @foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $dia)
                                    <option value="{{ $dia }}" {{ old('dia_descanso') === $dia ? 'selected' : '' }}>{{ $dia }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Motivo --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                            Observaciones / Motivo
                        </p>
                        <div class="field">
                            <label class="field-label" for="motivo">Descripción <span class="req">*</span></label>
                            <textarea name="motivo" id="motivo" class="field-textarea"
                                placeholder="Describe el motivo de la solicitud de forma clara..." required maxlength="500">{{ old('motivo') }}</textarea>
                            <div style="display:flex; justify-content:space-between; margin-top:4px;">
                                <span class="field-hint">Sé claro y específico.</span>
                                <span id="char-count">0 / 500</span>
                            </div>
                            @error('motivo')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Archivo adjunto --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                            Archivo Adjunto
                            <span class="opt" style="margin-left:4px;">Opcional</span>
                        </p>
                        <div class="dropzone" id="dropzone">
                            <input type="file" name="archivo" id="archivo" accept="image/*,.pdf" onchange="previewDoc(event)">
                            <div id="dz-content">
                                <div class="dropzone-icon">📎</div>
                                <p><strong>Haz clic para adjuntar</strong> o arrastra aquí</p>
                                <p style="margin-top:4px;font-size:12px;">Imagen o PDF · Máx 2MB</p>
                            </div>
                        </div>
                        <div id="preview"></div>
                        @error('archivo')<span class="field-error">⚠ {{ $message }}</span>@enderror
                    </div>

                    {{-- Errores generales --}}
                    @if($errors->any())
                        <div class="form-section">
                            <div class="error-list">
                                <p>Por favor corrige los siguientes errores:</p>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('permisos.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
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
        // Tipo de solicitud
        const tipoCards = { 'Permiso':'selected-permiso', 'Comision':'selected-comision', 'Suspension':'selected-suspension' };
        function selectTipo(valor, claseSeleccionada) {
            document.querySelectorAll('.tipo-card').forEach(c => {
                c.classList.remove('selected-permiso','selected-comision','selected-suspension');
            });
            event.currentTarget.classList.add(claseSeleccionada);
            event.currentTarget.querySelector('input').checked = true;
        }

        // Goce de sueldo
        function selectGoce(valor) {
            document.getElementById('goce-con').classList.remove('selected-con');
            document.getElementById('goce-sin').classList.remove('selected-sin');
            if (valor === 'Con Goce de Sueldo') {
                document.getElementById('goce-con').classList.add('selected-con');
            } else {
                document.getElementById('goce-sin').classList.add('selected-sin');
            }
            document.querySelector(`input[value="${valor}"]`).checked = true;
        }

        // Contador caracteres
        document.getElementById('motivo').addEventListener('input', function() {
            const n = this.value.length;
            const counter = document.getElementById('char-count');
            counter.textContent = n + ' / 500';
            counter.style.color = n > 450 ? 'var(--rose)' : 'var(--muted)';
        });

        // Calcular días entre fechas
        function calcularDias() {
            const ini = document.getElementById('fecha_inicio').value;
            const fin = document.getElementById('fecha_termino').value;
            if (!ini || !fin) { document.getElementById('dias-badge').style.display = 'none'; return; }
            const d1 = new Date(ini), d2 = new Date(fin);
            if (d2 < d1) { document.getElementById('dias-badge').style.display = 'none'; return; }
            const diff = Math.round((d2 - d1) / (1000*60*60*24)) + 1;
            document.getElementById('dias-count').textContent = diff;
            document.getElementById('dias-badge').style.display = 'block';
        }
        document.getElementById('fecha_inicio').addEventListener('change', calcularDias);
        document.getElementById('fecha_termino').addEventListener('change', calcularDias);

        // Preview archivo
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