<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('periodos.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Nuevo Período de Vacaciones</h2>
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
        .form-section { padding:26px 32px; border-bottom:1px solid #f0e6f5; }
        .form-section-footer { padding:20px 32px; background:var(--beige-light); border-top:1.5px solid var(--border); }
        .section-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); margin-bottom:18px; display:flex; align-items:center; gap:8px; }
        .section-title::after { content:''; flex:1; height:1px; background:linear-gradient(to right,#e0c8e8,transparent); }

        .field { display:flex; flex-direction:column; gap:5px; margin-bottom:18px; }
        .field-label { font-size:13px; font-weight:600; color:var(--brown); }
        .field-input,.field-select { height:42px; padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .field-input:focus,.field-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }

        /* Lista empleados */
        .search-employees { height:42px; padding:0 14px 0 38px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .search-employees:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }

        .employees-list { height:260px; overflow-y:auto; border:1.5px solid var(--border); border-radius:9px; background:#fff; }
        .employees-list::-webkit-scrollbar { width:5px; }
        .employees-list::-webkit-scrollbar-track { background:#f3eef5; }
        .employees-list::-webkit-scrollbar-thumb { background:var(--brand-mid); border-radius:3px; }

        .emp-item {
            display:flex; align-items:center; gap:10px;
            padding:10px 14px; border-bottom:1px solid #f5f0f7;
            cursor:pointer; transition:background .12s;
        }
        .emp-item:last-child { border-bottom:none; }
        .emp-item:hover { background:var(--brand-light); }
        .emp-item.checked { background:#f0e8f6; }

        .emp-item input[type="checkbox"] { display:none; }
        .custom-check {
            width:18px; height:18px; border-radius:5px;
            border:2px solid var(--border); flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            transition:all .15s; background:#fff;
        }
        .emp-item.checked .custom-check { background:var(--brand); border-color:var(--brand); }
        .custom-check svg { display:none; }
        .emp-item.checked .custom-check svg { display:block; }

        .emp-avatar-xs { width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg,var(--brand),var(--brand-mid)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:11px; font-weight:700; flex-shrink:0; }
        .emp-info p { margin:0; }
        .emp-name  { font-size:13px; font-weight:600; color:var(--text); }
        .emp-meta  { font-size:11px; color:var(--muted); }

        .select-all-row { display:flex; align-items:center; gap:8px; padding:10px 14px; background:var(--beige-light); border-bottom:1.5px solid var(--border); cursor:pointer; }
        .select-all-row label { font-size:12px; font-weight:700; color:var(--brand); text-transform:uppercase; letter-spacing:.05em; cursor:pointer; }

        /* Counter badge */
        .counter-badge { display:inline-flex; align-items:center; gap:5px; background:var(--brand); color:#fff; border-radius:20px; padding:3px 10px; font-size:12px; font-weight:700; }

        /* Resultados días */
        #resultados-dias { margin-top:16px; }
        .result-item {
            display:flex; align-items:center; justify-content:space-between;
            padding:10px 14px; border-radius:9px; background:var(--brand-light);
            border:1px solid #e0c8e8; margin-bottom:8px;
        }
        .result-emp  { font-size:13px; font-weight:600; color:var(--text); }
        .result-days { display:flex; gap:10px; }
        .day-tag { padding:2px 9px; border-radius:6px; font-size:12px; font-weight:600; }
        .day-tag-green { background:#e8f5ee; color:#1b6b38; }
        .day-tag-gold  { background:var(--gold-pale); color:var(--brown); }

        .loading-dots { display:none; gap:4px; align-items:center; padding:12px; }
        .loading-dots span { width:6px; height:6px; border-radius:50%; background:var(--brand-mid); animation:bounce .9s infinite; }
        .loading-dots span:nth-child(2) { animation-delay:.15s; }
        .loading-dots span:nth-child(3) { animation-delay:.3s; }
        @keyframes bounce { 0%,80%,100%{transform:translateY(0)} 40%{transform:translateY(-6px)} }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="form-card">
                <form method="POST" action="{{ route('periodos.store') }}" id="periodoForm">
                    @csrf

                    {{-- Selección de empleados --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Seleccionar Empleados
                            <span id="selected-count" class="counter-badge" style="display:none;">0 seleccionados</span>
                        </p>

                        {{-- Buscador --}}
                        <div style="position:relative; margin-bottom:10px;">
                            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#BBA4C0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                            </svg>
                            <input type="text" id="busqueda-empleado" placeholder="Buscar por nombre, clave o departamento..." class="search-employees">
                        </div>

                        {{-- Lista --}}
                        <div style="border:1.5px solid var(--border); border-radius:9px; overflow:hidden;">
                            <div class="select-all-row">
                                <div class="custom-check" id="all-check">
                                    <svg class="w-3 h-3" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <label onclick="toggleAll()">Seleccionar todos</label>
                                <input type="checkbox" id="seleccionar-todos" style="display:none;">
                            </div>
                            <div id="lista-empleados" class="employees-list" style="border-radius:0; border:none; border-top:1px solid var(--border);">
                                @foreach($usuarios as $usuario)
                                    <div class="emp-item empleado-item"
                                        data-name="{{ $usuario->name }}"
                                        data-clave="{{ $usuario->clave_empleado }}"
                                        data-departamento="{{ $usuario->departamento->name ?? '' }}"
                                        onclick="toggleItem(this)">
                                        <input type="checkbox" name="empleado_id[]" value="{{ $usuario->id }}" class="empleado-checkbox">
                                        <div class="custom-check">
                                            <svg class="w-3 h-3" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <div class="emp-avatar-xs">{{ strtoupper(substr($usuario->name,0,1)) }}</div>
                                        <div class="emp-info">
                                            <p class="emp-name">{{ $usuario->name }}</p>
                                            <p class="emp-meta">{{ $usuario->clave_empleado }} · {{ $usuario->departamento->name ?? 'Sin departamento' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Año --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Año del Período
                        </p>
                        <div class="field">
                            <label class="field-label" for="anio">Año <span style="color:var(--rose);">*</span></label>
                            <input type="number" name="anio" id="anio" class="field-input"
                                placeholder="{{ date('Y') }}" min="2000" max="2100" required>
                        </div>

                        {{-- Loading (fuera del div que se limpia) --}}
                        <div class="loading-dots" id="loading-dots">
                            <span></span><span></span><span></span>
                            <p style="font-size:12px; color:var(--muted); margin-left:4px;">Calculando días...</p>
                        </div>

                        {{-- Resultados calculados --}}
                        <div id="resultados-dias"></div>
                    </div>

                    {{-- Footer --}}
                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('periodos.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Guardar Período
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle item individual
        function toggleItem(el) {
            const cb = el.querySelector('input[type="checkbox"]');
            cb.checked = !cb.checked;
            el.classList.toggle('checked', cb.checked);
            updateCounter();
            fetchDays();
        }

        // Toggle todos
        function toggleAll() {
            const allCb    = document.getElementById('seleccionar-todos');
            const allCheck = document.getElementById('all-check');
            allCb.checked  = !allCb.checked;
            allCheck.classList.toggle('checked', allCb.checked);

            document.querySelectorAll('.empleado-item:not([style*="none"])').forEach(item => {
                const cb = item.querySelector('input[type="checkbox"]');
                cb.checked = allCb.checked;
                item.classList.toggle('checked', allCb.checked);
            });
            updateCounter();
            fetchDays();
        }

        function updateCounter() {
            const n = document.querySelectorAll('.empleado-checkbox:checked').length;
            const badge = document.getElementById('selected-count');
            badge.textContent = n + ' seleccionados';
            badge.style.display = n > 0 ? 'inline-flex' : 'none';
        }

        // Búsqueda en tiempo real
        document.getElementById('busqueda-empleado').addEventListener('input', function () {
            const f = this.value.toLowerCase();
            document.querySelectorAll('.empleado-item').forEach(item => {
                const match = item.dataset.name.toLowerCase().includes(f)
                    || item.dataset.clave.toLowerCase().includes(f)
                    || item.dataset.departamento.toLowerCase().includes(f);
                item.style.display = match ? '' : 'none';
            });
        });

        // Calcular días
        let debounce;
        function fetchDays() {
            clearTimeout(debounce);
            debounce = setTimeout(_fetch, 400);
        }

        function _fetch() {
            const anio = document.getElementById('anio').value;
            const ids  = Array.from(document.querySelectorAll('.empleado-checkbox:checked')).map(c => c.value);
            const div  = document.getElementById('resultados-dias');
            const load = document.getElementById('loading-dots');

            if (!ids.length || !anio) { div.innerHTML = ''; return; }

            load.style.display = 'flex';

            fetch('{{ route("calculate.days") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ anio, empleado_ids: ids })
            })
            .then(r => r.json())
            .then(data => {
                load.style.display = 'none';
                div.innerHTML = '';
                data.forEach(item => {
                    div.innerHTML += `
                        <div class="result-item">
                            <span class="result-emp">${item.empleado}</span>
                            <div class="result-days">
                                <span class="day-tag day-tag-green">✓ ${item.dias_corresponden} corresponden</span>
                                <span class="day-tag day-tag-gold">◎ ${item.dias_disponibles} disponibles</span>
                            </div>
                        </div>`;
                });
            })
            .catch(() => { load.style.display = 'none'; });
        }

        document.getElementById('anio').addEventListener('input', fetchDays);
        document.querySelectorAll('.empleado-checkbox').forEach(cb => cb.addEventListener('change', fetchDays));
    </script>
</x-app-layout>