<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('periodos.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar Período de Vacaciones</h2>
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
        }

        .form-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }

        /* Banner del período */
        .period-banner {
            padding:26px 32px;
            background:linear-gradient(135deg, var(--brand) 0%, #9b4dab 100%);
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;
            position:relative; overflow:hidden;
        }
        .period-banner::before { content:''; position:absolute; top:-40px; right:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.06); }
        .period-banner::after  { content:''; position:absolute; bottom:-50px; right:80px; width:160px; height:160px; border-radius:50%; background:rgba(214,166,68,.1); }

        .banner-left { display:flex; align-items:center; gap:16px; z-index:1; }
        .banner-avatar { width:56px; height:56px; border-radius:50%; border:3px solid rgba(255,255,255,.3); object-fit:cover; flex-shrink:0; }
        .banner-placeholder { width:56px; height:56px; border-radius:50%; border:3px solid rgba(255,255,255,.3); background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800; color:#fff; flex-shrink:0; }
        .banner-name { font-size:18px; font-weight:700; color:#fff; line-height:1.2; }
        .banner-sub  { font-size:13px; color:rgba(255,255,255,.75); margin-top:2px; }

        /* Stats en el banner */
        .banner-stats { display:flex; gap:12px; z-index:1; flex-wrap:wrap; }
        .stat-box { background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); border-radius:10px; padding:10px 16px; text-align:center; min-width:90px; }
        .stat-val  { font-size:22px; font-weight:800; color:#fff; line-height:1; }
        .stat-label{ font-size:11px; color:rgba(255,255,255,.75); margin-top:3px; font-weight:500; }

        /* Barra progreso días */
        .days-progress { margin-top:8px; }
        .days-track { height:6px; background:rgba(255,255,255,.2); border-radius:3px; overflow:hidden; margin-top:6px; }
        .days-fill  { height:100%; border-radius:3px; transition:width .4s ease; }

        .form-section { padding:26px 32px; border-bottom:1px solid #f0e6f5; }
        .form-section-footer { padding:20px 32px; background:var(--beige-light); border-top:1.5px solid var(--border); }
        .section-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); margin-bottom:18px; display:flex; align-items:center; gap:8px; }
        .section-title::after { content:''; flex:1; height:1px; background:linear-gradient(to right,#e0c8e8,transparent); }

        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        @media(max-width:540px){ .form-grid{grid-template-columns:1fr;} }

        .field { display:flex; flex-direction:column; gap:5px; }
        .field-label { font-size:13px; font-weight:600; color:var(--brown); display:flex; align-items:center; gap:5px; }
        .req { color:var(--rose); }
        .readonly-badge { font-size:10px; font-weight:600; background:#f0e6f5; color:var(--muted); border-radius:4px; padding:1px 6px; }

        .field-input,.field-select { height:42px; padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .field-input:focus,.field-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-input[readonly] { background:var(--beige-light); color:var(--muted); cursor:not-allowed; border-color:#ede3f2; }

        /* Spinner días disponibles */
        .number-field { position:relative; }
        .number-field .field-input { padding-right:80px; font-size:16px; font-weight:700; color:var(--brand); }
        .number-controls { position:absolute; right:10px; top:50%; transform:translateY(-50%); display:flex; gap:4px; }
        .num-btn { width:28px; height:28px; border-radius:6px; border:1.5px solid var(--border); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; color:var(--muted); transition:all .15s; }
        .num-btn:hover { border-color:var(--brand); color:var(--brand); background:var(--brand-light); }

        /* Hint con distribución */
        .days-hint { margin-top:8px; padding:10px 14px; background:var(--beige-light); border-radius:8px; border:1px solid var(--border); }
        .days-hint p { margin:0; font-size:12px; color:var(--muted); }
        .days-hint strong { color:var(--brown); }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="form-card">

                @php
                    $disp = $periodo->dias_disponibles ?? 0;
                    $corr = $periodo->dias_corresponden ?? 0;
                    $usados = $corr - $disp;
                    $pct = $corr > 0 ? min(100, ($disp / $corr) * 100) : 0;
                    $fillColor = $pct > 50 ? '#4ade80' : ($pct > 20 ? '#EED39B' : '#f87171');
                @endphp

                {{-- Banner --}}
                <div class="period-banner">
                    <div class="banner-left">
                        @if($periodo->empleado && $periodo->empleado->foto_perfil)
                            <img src="{{ asset('storage/' . $periodo->empleado->foto_perfil) }}" class="banner-avatar" alt="">
                        @else
                            <div class="banner-placeholder">{{ strtoupper(substr($periodo->empleado->name ?? 'N', 0, 1)) }}</div>
                        @endif
                        <div>
                            <p class="banner-name">{{ $periodo->empleado->name ?? 'N/A' }}</p>
                            <p class="banner-sub">{{ $periodo->empleado->puesto_empleado ?? '' }} · Período {{ $periodo->anio }}</p>
                            <div class="days-progress" style="width:140px;">
                                <p style="font-size:11px; color:rgba(255,255,255,.7); margin:0;">
                                    {{ $disp }} de {{ $corr }} días disponibles
                                </p>
                                <div class="days-track">
                                    <div class="days-fill" style="width:{{ $pct }}%; background:{{ $fillColor }};"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="banner-stats">
                        <div class="stat-box">
                            <div class="stat-val">{{ $corr }}</div>
                            <div class="stat-label">Corresponden</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-val">{{ $usados }}</div>
                            <div class="stat-label">Usados</div>
                        </div>
                        <div class="stat-box" style="background:rgba(214,166,68,.25); border-color:rgba(214,166,68,.4);">
                            <div class="stat-val" style="color:var(--gold-light);">{{ $disp }}</div>
                            <div class="stat-label">Disponibles</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('periodos.update', $periodo->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Info de solo lectura --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Información del Período
                        </p>
                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label">
                                    Empleado
                                    <span class="readonly-badge">Solo lectura</span>
                                </label>
                                <input type="text" value="{{ $periodo->empleado->name ?? 'N/A' }}" class="field-input" readonly>
                            </div>
                            <div class="field">
                                <label class="field-label">
                                    Año del Período
                                    <span class="readonly-badge">Solo lectura</span>
                                </label>
                                <input type="text" value="{{ $periodo->anio }}" class="field-input" readonly>
                            </div>
                            <div class="field">
                                <label class="field-label">
                                    Días que Corresponden
                                    <span class="readonly-badge">Solo lectura</span>
                                </label>
                                <input type="text" value="{{ $corr }} días" class="field-input" readonly>
                            </div>
                            <div class="field">
                                <label class="field-label">Días ya Utilizados</label>
                                <input type="text" value="{{ $usados }} días" class="field-input" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Campo editable --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Ajuste Manual
                        </p>

                        <div class="field">
                            <label class="field-label" for="dias_disponibles">
                                Días Disponibles <span class="req">*</span>
                            </label>
                            <div class="number-field">
                                <input type="number" name="dias_disponibles" id="dias_disponibles"
                                    value="{{ $disp }}" class="field-input"
                                    min="0" max="{{ $corr }}" required>
                                <div class="number-controls">
                                    <button type="button" class="num-btn" onclick="adjust(-1)">−</button>
                                    <button type="button" class="num-btn" onclick="adjust(1)">+</button>
                                </div>
                            </div>
                            <div class="days-hint">
                                <p>Máximo permitido: <strong>{{ $corr }} días</strong> · Días usados hasta ahora: <strong>{{ $usados }}</strong></p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('periodos.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" id="save-btn" class="btn btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const input   = document.getElementById('dias_disponibles');
        const saveBtn = document.getElementById('save-btn');
        const hint    = document.querySelector('.days-hint p');
        const MAX     = parseInt(input.max) || 999;

        function validate() {
            const val = parseInt(input.value) || 0;
            const ok  = val >= 0 && val <= MAX;

            // Visual feedback en el input
            input.style.borderColor  = ok ? 'var(--border)' : 'var(--rose)';
            input.style.background   = ok ? '#fff' : 'var(--error-bg, #fce8ee)';
            input.style.color        = ok ? 'var(--brand)' : 'var(--rose)';
            input.style.boxShadow    = ok ? '' : '0 0 0 3px rgba(170,73,105,.15)';

            // Mensaje en el hint
            if (val < 0) {
                hint.innerHTML = `<span style="color:var(--rose);">⚠ El mínimo permitido es <strong>0 días</strong>.</span>`;
            } else if (val > MAX) {
                hint.innerHTML = `<span style="color:var(--rose);">⚠ No puede superar los <strong>${MAX} días correspondientes</strong>.</span>`;
            } else {
                hint.innerHTML = `Máximo permitido: <strong>${MAX} días</strong> · Días usados hasta ahora: <strong>${MAX - val}</strong>`;
            }

            // Bloquear / habilitar botón guardar
            saveBtn.disabled = !ok;
            saveBtn.style.opacity = ok ? '1' : '0.45';
            saveBtn.style.cursor  = ok ? 'pointer' : 'not-allowed';
            saveBtn.style.transform = ok ? '' : 'none';
            saveBtn.style.boxShadow = ok ? '' : 'none';
        }

        function adjust(delta) {
            const val = parseInt(input.value) || 0;
            input.value = Math.max(0, Math.min(MAX, val + delta));
            validate();
        }

        input.addEventListener('input', validate);
        validate(); // Correr al cargar
    </script>
</x-app-layout>