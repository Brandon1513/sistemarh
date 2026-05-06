<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('vacaciones.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Solicitar Vacaciones</h2>
        </div>
    </x-slot>

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

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
        .readonly-badge { font-size:10px; font-weight:600; background:#f0e6f5; color:var(--muted); border-radius:4px; padding:1px 6px; }

        .field-input,.field-select { padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; height:42px; }
        .field-input:focus,.field-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-input[readonly] { background:var(--beige-light); color:var(--muted); cursor:not-allowed; border-color:#ede3f2; }
        .field-hint { font-size:12px; color:var(--muted); }
        .field-error { font-size:12px; color:var(--error); }

        /* Selector de período mejorado */
        .periodo-card {
            border:1.5px solid var(--border); border-radius:9px; overflow:hidden;
        }
        .periodo-option {
            display:flex; align-items:center; justify-content:space-between;
            padding:12px 16px; cursor:pointer; transition:background .12s;
            border-bottom:1px solid #f0e6f5;
        }
        .periodo-option:last-child { border-bottom:none; }
        .periodo-option:hover { background:var(--brand-light); }
        .periodo-option.selected { background:var(--brand-light); border-left:3px solid var(--brand); }
        .periodo-option input[type="radio"] { display:none; }
        .periodo-year { font-size:15px; font-weight:700; color:var(--brand); }
        .periodo-dias { display:flex; gap:10px; align-items:center; }
        .dias-disp { padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; background:#e8f5ee; color:#1b6b38; }
        .dias-corr { padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; background:var(--brand-light); color:var(--brand); }

        /* Resumen de días */
        .days-summary { padding:14px 16px; background:var(--brand-light); border:1px solid var(--border); border-radius:10px; display:flex; gap:16px; flex-wrap:wrap; }
        .day-stat { display:flex; flex-direction:column; gap:2px; }
        .day-stat-val { font-size:20px; font-weight:800; color:var(--brand); line-height:1; }
        .day-stat-lbl { font-size:11px; color:var(--muted); }

        /* Validación días */
        .dias-warning { display:none; margin-top:6px; padding:8px 12px; background:#fce8ee; border:1px solid #f0b3c3; border-radius:8px; font-size:12px; color:var(--rose); }

        /* Flatpickr override */
        .flatpickr-input { height:42px !important; padding:0 14px !important; border:1.5px solid var(--border) !important; border-radius:9px !important; font-size:14px !important; color:var(--text) !important; background:#fff !important; outline:none !important; width:100% !important; }
        .flatpickr-input:focus { border-color:var(--brand) !important; box-shadow:0 0 0 3px rgba(106,44,117,.12) !important; }
        .flatpickr-day.selected { background:var(--brand) !important; border-color:var(--brand) !important; }
        .flatpickr-day.inRange  { background:var(--brand-light) !important; border-color:var(--brand-light) !important; }

        /* Modal aviso */
        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:50; display:flex; align-items:center; justify-content:center; }
        .modal-box { background:#fff; border-radius:16px; padding:32px; max-width:460px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.25); }
        .modal-title { font-size:18px; font-weight:700; color:var(--text); margin-bottom:12px; }
        .modal-body  { font-size:14px; color:var(--muted); line-height:1.6; margin-bottom:20px; }
        .modal-check { display:flex; align-items:flex-start; gap:10px; padding:12px 14px; background:var(--beige-light); border:1px solid var(--border); border-radius:9px; cursor:pointer; margin-bottom:20px; }
        .modal-check input { display:none; }
        .m-check { width:18px; height:18px; border-radius:5px; border:2px solid var(--border); flex-shrink:0; display:flex; align-items:center; justify-content:center; margin-top:1px; transition:all .15s; background:#fff; }
        .modal-check.checked .m-check { background:var(--brand); border-color:var(--brand); }
        .m-svg { display:none; }
        .modal-check.checked .m-svg { display:block; }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-primary:disabled { opacity:.45; cursor:not-allowed; transform:none !important; box-shadow:none !important; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }
    </style>

    {{-- Modal de aviso --}}
    <div class="modal-overlay" id="modal-aviso">
        <div class="modal-box">
            <p class="modal-title">📋 Aviso Importante</p>
            <p class="modal-body">
                Las solicitudes de vacaciones deben realizarse con un <strong>mínimo de 7 días de anticipación</strong>.
                Al continuar, confirmas que estás de acuerdo con este lineamiento y que la información proporcionada es correcta.
            </p>
            <label class="modal-check" id="modal-check-wrap" onclick="toggleModalCheck()">
                <input type="checkbox" id="modal-accept">
                <div class="m-check">
                    <svg class="w-3 h-3 m-svg" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span style="font-size:13px; color:var(--text);">Entiendo y acepto el lineamiento de 7 días de anticipación.</span>
            </label>
            <div style="text-align:right;">
                <button id="modal-proceed" class="btn btn-primary" disabled onclick="closeModal()">
                    Continuar con la solicitud
                </button>
            </div>
        </div>
    </div>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="form-card">
                <form method="POST" action="{{ route('vacaciones.store') }}" id="vacForm">
                    @csrf
                    <input type="hidden" name="estado" value="pendiente">

                    {{-- Datos del solicitante (readonly) --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Datos del Solicitante
                        </p>

                        {{-- Banner empleado --}}
                        <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; background:var(--brand-light); border:1px solid var(--border); border-radius:10px; margin-bottom:18px;">
                            @if($user->foto_perfil)
                                <img src="{{ asset('storage/' . $user->foto_perfil) }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid var(--brand-mid);" alt="">
                            @else
                                <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--brand),var(--brand-mid));display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <p style="font-weight:700;font-size:14px;color:var(--text);margin:0;">{{ $user->name }}</p>
                                <p style="font-size:12px;color:var(--muted);margin:0;">
                                    {{ $user->puesto_empleado ?? '' }} · {{ $departamentoNombre }} · Ingreso: {{ $user->fecha_ingreso ? \Carbon\Carbon::parse($user->fecha_ingreso)->format('d/m/Y') : '—' }}
                                </p>
                            </div>
                            <span style="margin-left:auto;font-size:11px;font-weight:600;background:#fff;border:1px solid var(--brand-mid);color:var(--brand);border-radius:6px;padding:2px 8px;">Autocompletado</span>
                        </div>

                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label">Nombre <span class="readonly-badge">Solo lectura</span></label>
                                <input type="text" class="field-input" value="{{ $user->name }}" readonly>
                            </div>
                            <div class="field">
                                <label class="field-label">Clave de Empleado <span class="readonly-badge">Solo lectura</span></label>
                                <input type="text" class="field-input" value="{{ $user->clave_empleado }}" readonly>
                            </div>
                            <div class="field">
                                <label class="field-label">Departamento <span class="readonly-badge">Solo lectura</span></label>
                                <input type="text" class="field-input" value="{{ $departamentoNombre }}" readonly>
                            </div>
                            <div class="field">
                                <label class="field-label">Fecha de Ingreso <span class="readonly-badge">Solo lectura</span></label>
                                <input type="text" class="field-input"
                                    value="{{ $user->fecha_ingreso ? \Carbon\Carbon::parse($user->fecha_ingreso)->format('d/m/Y') : '—' }}" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Período correspondiente --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Período Correspondiente
                        </p>

                        @if($periodos->isEmpty())
                            <div style="padding:16px; background:var(--beige-light); border:1px dashed var(--border); border-radius:9px; font-size:13px; color:var(--muted); text-align:center;">
                                No tienes períodos de vacaciones activos disponibles.
                            </div>
                        @else
                            <div class="periodo-card">
                                @foreach($periodos as $periodo)
                                    <label class="periodo-option {{ $loop->first ? 'selected' : '' }}"
                                        id="opt-{{ $periodo->anio }}"
                                        onclick="selectPeriodo(this, {{ $periodo->dias_disponibles }}, {{ $periodo->dias_corresponden }})">
                                        <input type="radio" name="periodo_correspondiente" value="{{ $periodo->anio }}"
                                            data-disponibles="{{ $periodo->dias_disponibles }}"
                                            data-corresponden="{{ $periodo->dias_corresponden }}"
                                            {{ $loop->first ? 'checked' : '' }}>
                                        <span class="periodo-year">Período {{ $periodo->anio }}</span>
                                        <div class="periodo-dias">
                                            <span class="dias-corr">{{ $periodo->dias_corresponden }} corresponden</span>
                                            <span class="dias-disp">{{ $periodo->dias_disponibles }} disponibles</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            {{-- Resumen de días --}}
                            <div class="days-summary" style="margin-top:14px;" id="days-summary">
                                <div class="day-stat">
                                    <span class="day-stat-val" id="stat-corresponden">{{ $periodos->first()->dias_corresponden }}</span>
                                    <span class="day-stat-lbl">Corresponden</span>
                                </div>
                                <div class="day-stat">
                                    <span class="day-stat-val" id="stat-disponibles">{{ $periodos->first()->dias_disponibles }}</span>
                                    <span class="day-stat-lbl">Disponibles</span>
                                </div>
                                <div class="day-stat">
                                    <span class="day-stat-val" id="stat-solicitados" style="color:var(--gold);">0</span>
                                    <span class="day-stat-lbl">Solicitados</span>
                                </div>
                                <div class="day-stat">
                                    <span class="day-stat-val" id="stat-pendientes">{{ $periodos->first()->dias_disponibles }}</span>
                                    <span class="day-stat-lbl">Quedarán disponibles</span>
                                </div>
                            </div>

                            {{-- Inputs hidden para enviar al store --}}
                            <input type="hidden" name="dias_corresponden" id="hidden-corresponden" value="{{ $periodos->first()->dias_corresponden }}">
                            <input type="hidden" name="pendientes_disfrutar" id="hidden-pendientes" value="{{ $periodos->first()->dias_disponibles }}">
                        @endif
                    </div>

                    {{-- Días solicitados --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                            Días Solicitados
                        </p>
                        <div class="field">
                            <label class="field-label" for="dias_solicitados">Número de días <span class="req">*</span></label>
                            <input type="number" name="dias_solicitados" id="dias_solicitados" class="field-input"
                                min="1" placeholder="Ej. 5" required>
                            <div class="dias-warning" id="dias-warning">
                                ⚠ No puedes solicitar más días de los disponibles.
                            </div>
                            <span class="field-hint">Ingresa el número de días hábiles que deseas tomar.</span>
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Fechas de Vacaciones
                        </p>
                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label" for="fecha_inicio_vacaciones">Fecha de Inicio <span class="req">*</span></label>
                                <input type="text" name="fecha_inicio_vacaciones" id="fecha_inicio_vacaciones" class="field-input" placeholder="Selecciona fecha" required>
                                <span class="field-hint">No incluye domingos ni días festivos.</span>
                                @error('fecha_inicio_vacaciones')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="fecha_termino_vacaciones">Fecha de Término <span class="req">*</span></label>
                                <input type="text" name="fecha_termino_vacaciones" id="fecha_termino_vacaciones" class="field-input" placeholder="Selecciona fecha" required>
                                @error('fecha_termino_vacaciones')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="col-span-2 field">
                                <label class="field-label" for="fecha_presentarse_trabajar">Fecha de Reincorporación <span class="req">*</span></label>
                                <input type="text" name="fecha_presentarse_trabajar" id="fecha_presentarse_trabajar" class="field-input" placeholder="Selecciona fecha" required>
                                <span class="field-hint">Día en que regresarás a laborar.</span>
                                @error('fecha_presentarse_trabajar')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Errores --}}
                    @if($errors->any())
                        <div class="form-section">
                            <div style="background:#fce8ee; border:1px solid #f0b3c3; border-radius:10px; padding:14px 16px;">
                                <p style="font-size:13px; font-weight:700; color:var(--rose); margin-bottom:8px;">Por favor corrige los siguientes errores:</p>
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li style="font-size:13px; color:#7a2039; margin-left:12px;">• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    {{-- Footer --}}
                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('vacaciones.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" id="submit-btn" class="btn btn-primary">
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
        // ── Modal de aviso ──────────────────────────────────────────────────
        function toggleModalCheck() {
            const wrap = document.getElementById('modal-check-wrap');
            const cb   = document.getElementById('modal-accept');
            cb.checked = !cb.checked;
            wrap.classList.toggle('checked', cb.checked);
            document.getElementById('modal-proceed').disabled = !cb.checked;
        }
        function closeModal() {
            document.getElementById('modal-aviso').style.display = 'none';
        }

        // ── Selección de período ─────────────────────────────────────────────
        let diasDisponibles = {{ $periodos->isNotEmpty() ? $periodos->first()->dias_disponibles : 0 }};

        function selectPeriodo(el, disp, corr) {
            document.querySelectorAll('.periodo-option').forEach(o => o.classList.remove('selected'));
            el.classList.add('selected');
            el.querySelector('input').checked = true;
            diasDisponibles = disp;
            document.getElementById('stat-corresponden').textContent = corr;
            document.getElementById('stat-disponibles').textContent  = disp;
            document.getElementById('hidden-corresponden').value = corr;
            actualizarDias();
        }

        // ── Días solicitados ────────────────────────────────────────────────
        document.getElementById('dias_solicitados')?.addEventListener('input', actualizarDias);

        function actualizarDias() {
            const sol = parseInt(document.getElementById('dias_solicitados').value) || 0;
            const pend = diasDisponibles - sol;
            const warn = document.getElementById('dias-warning');
            const btn  = document.getElementById('submit-btn');

            document.getElementById('stat-solicitados').textContent = sol;
            document.getElementById('stat-pendientes').textContent  = pend;
            document.getElementById('hidden-pendientes').value = pend;

            const invalid = sol > diasDisponibles || sol < 1;
            warn.style.display = sol > diasDisponibles ? 'block' : 'none';

            const solInput = document.getElementById('dias_solicitados');
            solInput.style.borderColor = invalid && sol > 0 ? 'var(--rose)' : 'var(--border)';
            solInput.style.background  = invalid && sol > 0 ? '#fce8ee' : '#fff';

            btn.disabled = sol > diasDisponibles;
            btn.style.opacity = btn.disabled ? '.45' : '1';
            btn.style.cursor  = btn.disabled ? 'not-allowed' : 'pointer';
        }

        // ── Flatpickr ───────────────────────────────────────────────────────
        const diasFestivos = [
            '{{ date("Y") }}-01-01', '{{ date("Y") }}-02-03', '{{ date("Y") }}-03-17',
            '{{ date("Y") }}-04-18', '{{ date("Y") }}-04-19', '{{ date("Y") }}-05-01',
            '{{ date("Y") }}-09-16', '{{ date("Y") }}-10-12', '{{ date("Y") }}-11-17',
            '{{ date("Y") }}-12-24', '{{ date("Y") }}-12-25', '{{ date("Y") }}-12-31'
        ];

        const fpConfig = {
            dateFormat: 'Y-m-d',
            locale: 'es',
            minDate: 'today',
            disable: [d => d.getDay() === 0 || diasFestivos.includes(d.toISOString().split('T')[0])],
        };

        flatpickr('#fecha_inicio_vacaciones',    { ...fpConfig });
        flatpickr('#fecha_termino_vacaciones',   { ...fpConfig });
        flatpickr('#fecha_presentarse_trabajar', { ...fpConfig });
    </script>
</x-app-layout>