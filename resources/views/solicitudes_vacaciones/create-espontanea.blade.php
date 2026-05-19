<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('solicitudes_vacaciones.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Registrar Vacación Espontánea</h2>
        </div>
    </x-slot>

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <style>
        :root {
            --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5;
            --brand-mid:#BBA4C0; --gold:#D6A644; --gold-light:#EED39B;
            --gold-pale:#faf3e0; --brown:#473524; --rose:#AA4969;
            --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30;
            --muted:#7a6682; --error:#AA4969; --error-bg:#fce8ee;
        }

        .form-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }

        /* Banner informativo RH */
        .rh-banner {
            padding:16px 24px;
            background:linear-gradient(135deg, #473524 0%, #6A2C75 100%);
            display:flex; align-items:center; gap:12px;
        }
        .rh-banner-icon { font-size:22px; }
        .rh-banner-text { font-size:13px; color:rgba(255,255,255,.9); line-height:1.5; }
        .rh-banner-text strong { color:#EED39B; }

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
        .field-input,.field-select { padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; height:42px; }
        .field-input:focus,.field-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-error { font-size:12px; color:var(--error); }
        .field-hint  { font-size:12px; color:var(--muted); }

        /* Selector de empleado con avatar */
        .employee-select-wrap { position:relative; }
        .employee-preview {
            display:none; align-items:center; gap:12px;
            padding:12px 14px; background:var(--brand-light);
            border:1px solid var(--border); border-radius:10px; margin-top:8px;
        }
        .employee-preview.show { display:flex; }
        .emp-av { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,var(--brand),var(--brand-mid)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:14px; font-weight:700; flex-shrink:0; }
        .emp-name { font-size:13px; font-weight:700; color:var(--text); }
        .emp-meta { font-size:11px; color:var(--muted); }

        /* Período cards */
        .periodo-card { border:1.5px solid var(--border); border-radius:9px; overflow:hidden; }
        .periodo-option { display:flex; align-items:center; justify-content:space-between; padding:11px 16px; cursor:pointer; transition:background .12s; border-bottom:1px solid #f0e6f5; }
        .periodo-option:last-child { border-bottom:none; }
        .periodo-option:hover { background:var(--brand-light); }
        .periodo-option.selected { background:var(--brand-light); border-left:3px solid var(--brand); }
        .periodo-option input[type="radio"] { display:none; }
        .periodo-year { font-size:15px; font-weight:700; color:var(--brand); }
        .periodo-dias { display:flex; gap:8px; }
        .dias-disp { padding:2px 9px; border-radius:20px; font-size:12px; font-weight:700; background:#e8f5ee; color:#1b6b38; }
        .dias-corr { padding:2px 9px; border-radius:20px; font-size:12px; font-weight:600; background:var(--brand-light); color:var(--brand); }
        .no-periodos { padding:16px; background:var(--beige-light); border:1px dashed var(--border); border-radius:9px; font-size:13px; color:var(--muted); text-align:center; }

        /* Resumen días */
        .days-summary { padding:14px 16px; background:var(--brand-light); border:1px solid var(--border); border-radius:10px; display:flex; gap:16px; flex-wrap:wrap; margin-top:12px; }
        .day-stat { display:flex; flex-direction:column; gap:2px; }
        .day-stat-val { font-size:20px; font-weight:800; color:var(--brand); line-height:1; }
        .day-stat-lbl { font-size:11px; color:var(--muted); }
        .dias-warning { display:none; margin-top:6px; padding:8px 12px; background:#fce8ee; border:1px solid #f0b3c3; border-radius:8px; font-size:12px; color:var(--rose); }

        /* Flatpickr override */
        .flatpickr-input { height:42px !important; padding:0 14px !important; border:1.5px solid var(--border) !important; border-radius:9px !important; font-size:14px !important; color:var(--text) !important; background:#fff !important; outline:none !important; width:100% !important; }
        .flatpickr-input:focus { border-color:var(--brand) !important; box-shadow:0 0 0 3px rgba(106,44,117,.12) !important; }
        .flatpickr-day.selected { background:var(--brand) !important; border-color:var(--brand) !important; }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-primary:disabled { opacity:.45; cursor:not-allowed; transform:none !important; box-shadow:none !important; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }

        .error-box { background:#fce8ee; border:1px solid #f0b3c3; border-radius:10px; padding:14px 16px; margin-bottom:16px; }
        .error-box p { font-size:13px; font-weight:700; color:var(--rose); margin-bottom:6px; }
        .error-box li { font-size:13px; color:#7a2039; margin-left:14px; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="error-box" style="margin-bottom:16px;">
                    <p>Por favor corrige los siguientes errores:</p>
                    <ul>@foreach($errors->all() as $e)<li>⚠️ {{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="form-card">

                {{-- Banner RH --}}
                <div class="rh-banner">
                    <span class="rh-banner-icon">📋</span>
                    <p class="rh-banner-text">
                        <strong>Registro por Recursos Humanos.</strong>
                        Esta solicitud se registrará en nombre del empleado. Se permite cualquier fecha, incluyendo fechas pasadas. El jefe directo recibirá notificación para aprobar o rechazar.
                    </p>
                </div>

                <form method="POST" action="{{ route('vacaciones.espontanea.store') }}" id="espForm">
                    @csrf
                    <input type="hidden" name="tipo" value="espontanea">
                    <input type="hidden" name="estado" value="pendiente">
                    <input type="hidden" name="creado_por" value="{{ auth()->id() }}">

                    {{-- Selección de empleado --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Empleado
                        </p>
                        <div class="field">
                            <label class="field-label" for="empleado_id">Selecciona el empleado <span class="req">*</span></label>
                            <div class="employee-select-wrap">
                                <select name="empleado_id" id="empleado_id" class="field-select" required onchange="onEmpleadoChange(this)">
                                    <option value="">Seleccionar empleado...</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}"
                                            data-name="{{ $empleado->name }}"
                                            data-puesto="{{ $empleado->puesto_empleado ?? '' }}"
                                            data-depto="{{ $empleado->departamento->name ?? '' }}"
                                            {{ old('empleado_id') == $empleado->id ? 'selected' : '' }}>
                                            {{ $empleado->name }} — {{ $empleado->clave_empleado }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="employee-preview" id="emp-preview">
                                    <div class="emp-av" id="emp-av">?</div>
                                    <div>
                                        <p class="emp-name" id="emp-name">—</p>
                                        <p class="emp-meta" id="emp-meta">—</p>
                                    </div>
                                </div>
                            </div>
                            @error('empleado_id')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>

                        {{-- Períodos del empleado (carga dinámica) --}}
                        <div class="field" style="margin-top:16px;" id="periodos-wrap" style="display:none;">
                            <label class="field-label">Período correspondiente <span class="req">*</span></label>
                            <div id="periodos-container">
                                <div class="no-periodos">Selecciona un empleado para ver sus períodos disponibles.</div>
                            </div>
                            <input type="hidden" name="periodo_correspondiente" id="periodo_hidden">

                            {{-- Resumen días --}}
                            <div class="days-summary" id="days-summary" style="display:none;">
                                <div class="day-stat">
                                    <span class="day-stat-val" id="stat-corresponden">—</span>
                                    <span class="day-stat-lbl">Corresponden</span>
                                </div>
                                <div class="day-stat">
                                    <span class="day-stat-val" id="stat-disponibles">—</span>
                                    <span class="day-stat-lbl">Disponibles</span>
                                </div>
                                <div class="day-stat">
                                    <span class="day-stat-val" id="stat-solicitados" style="color:var(--gold);">0</span>
                                    <span class="day-stat-lbl">Solicitados</span>
                                </div>
                                <div class="day-stat">
                                    <span class="day-stat-val" id="stat-pendientes">—</span>
                                    <span class="day-stat-lbl">Quedarán disponibles</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Días y fechas --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Fechas — Se permiten fechas pasadas
                        </p>
                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label" for="dias_solicitados">Días solicitados <span class="req">*</span></label>
                                <input type="number" name="dias_solicitados" id="dias_solicitados" class="field-input" min="1" placeholder="Ej. 2" required oninput="actualizarDias()">
                                <div class="dias-warning" id="dias-warning">⚠ No puedes solicitar más días de los disponibles.</div>
                                @error('dias_solicitados')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                {{-- spacer --}}
                            </div>
                            <div class="field">
                                <label class="field-label" for="fecha_inicio_vacaciones">Fecha de Inicio <span class="req">*</span></label>
                                <input type="text" name="fecha_inicio_vacaciones" id="fecha_inicio_vacaciones" class="field-input" placeholder="Selecciona fecha" required>
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
                                <span class="field-hint">Día en que regresará a laborar.</span>
                                @error('fecha_presentarse_trabajar')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('solicitudes_vacaciones.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" id="submit-btn" class="btn btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Registrar Vacación
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Datos de períodos por empleado pasados desde el controller
        const periodosPorEmpleado = @json($periodosPorEmpleado);

        let diasDisponibles = 0;

        function onEmpleadoChange(sel) {
            const opt = sel.options[sel.selectedIndex];
            const id  = sel.value;

            // Preview empleado
            if (id) {
                document.getElementById('emp-av').textContent   = opt.dataset.name.charAt(0).toUpperCase();
                document.getElementById('emp-name').textContent = opt.dataset.name;
                document.getElementById('emp-meta').textContent = opt.dataset.puesto + (opt.dataset.depto ? ' · ' + opt.dataset.depto : '');
                document.getElementById('emp-preview').classList.add('show');
            } else {
                document.getElementById('emp-preview').classList.remove('show');
            }

            // Cargar períodos
            const periodos = periodosPorEmpleado[id] || [];
            const container = document.getElementById('periodos-container');
            const summary   = document.getElementById('days-summary');

            if (!periodos.length) {
                container.innerHTML = '<div class="no-periodos">Este empleado no tiene períodos de vacaciones registrados.</div>';
                summary.style.display = 'none';
                diasDisponibles = 0;
                return;
            }

            let html = '<div class="periodo-card">';
            periodos.forEach((p, i) => {
                html += `
                    <label class="periodo-option ${i === 0 ? 'selected' : ''}" onclick="selectPeriodo(this, ${p.dias_disponibles}, ${p.dias_corresponden}, ${p.anio})">
                        <input type="radio" name="_periodo_radio" value="${p.anio}" ${i === 0 ? 'checked' : ''}>
                        <span class="periodo-year">Período ${p.anio}</span>
                        <div class="periodo-dias">
                            <span class="dias-corr">${p.dias_corresponden} corresponden</span>
                            <span class="dias-disp">${p.dias_disponibles} disponibles</span>
                        </div>
                    </label>`;
            });
            html += '</div>';
            container.innerHTML = html;

            // Seleccionar el primero por defecto
            const first = periodos[0];
            selectPeriodo(null, first.dias_disponibles, first.dias_corresponden, first.anio);
            document.getElementById('periodos-wrap').style.display = 'block';
        }

        function selectPeriodo(el, disp, corr, anio) {
            if (el) {
                document.querySelectorAll('.periodo-option').forEach(o => o.classList.remove('selected'));
                el.classList.add('selected');
                el.querySelector('input').checked = true;
            }
            diasDisponibles = disp;
            document.getElementById('periodo_hidden').value        = anio;
            document.getElementById('stat-corresponden').textContent = corr;
            document.getElementById('stat-disponibles').textContent  = disp;
            document.getElementById('days-summary').style.display   = 'flex';
            actualizarDias();
        }

        function actualizarDias() {
            const sol  = parseInt(document.getElementById('dias_solicitados').value) || 0;
            const pend = diasDisponibles - sol;
            const warn = document.getElementById('dias-warning');
            const btn  = document.getElementById('submit-btn');

            document.getElementById('stat-solicitados').textContent = sol;
            document.getElementById('stat-pendientes').textContent  = pend;

            const invalid = sol > diasDisponibles || sol < 1;
            warn.style.display = sol > diasDisponibles ? 'block' : 'none';

            const inp = document.getElementById('dias_solicitados');
            inp.style.borderColor = invalid && sol > 0 ? 'var(--rose)' : 'var(--border)';
            inp.style.background  = invalid && sol > 0 ? '#fce8ee' : '#fff';

            btn.disabled = sol > diasDisponibles;
        }

        // Flatpickr — sin restricción de fecha mínima para RH
        const diasFestivos = [
            '{{ date("Y") }}-01-01','{{ date("Y") }}-02-03','{{ date("Y") }}-03-17',
            '{{ date("Y") }}-04-18','{{ date("Y") }}-04-19','{{ date("Y") }}-05-01',
            '{{ date("Y") }}-09-16','{{ date("Y") }}-10-12','{{ date("Y") }}-11-17',
            '{{ date("Y") }}-12-24','{{ date("Y") }}-12-25','{{ date("Y") }}-12-31'
        ];
        const fpConfig = {
            dateFormat: 'Y-m-d',
            locale: 'es',
            // Sin minDate — RH puede registrar fechas pasadas
            disable: [d => d.getDay() === 0 || diasFestivos.includes(d.toISOString().split('T')[0])],
        };
        flatpickr('#fecha_inicio_vacaciones',    fpConfig);
        flatpickr('#fecha_termino_vacaciones',   fpConfig);
        flatpickr('#fecha_presentarse_trabajar', fpConfig);

        // Si hay old value al cargar
        window.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('empleado_id');
            if (sel.value) onEmpleadoChange(sel);
        });
    </script>
</x-app-layout>