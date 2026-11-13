@php
    $v = fn ($campo) => old($campo, $valores[$campo] ?? null);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('comunicados.admin.index') }}" style="color:#BBA4C0;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">🎉 Invitación al festejo de cumpleaños</h2>
        </div>
    </x-slot>

    <style>
        :root { --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0; --gold:#D6A644; --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682; }
        .layout { display:grid; grid-template-columns:minmax(0,1fr) 420px; gap:20px; align-items:start; }
        @media(max-width:1000px){ .layout { grid-template-columns:minmax(0,1fr); } }
        .card { background:#fff; border-radius:16px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); }
        .card-body { padding:24px 26px; display:flex; flex-direction:column; gap:18px; }
        .card-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); }
        .grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        @media(max-width:600px){ .grid2 { grid-template-columns:1fr; } }
        .field { display:flex; flex-direction:column; gap:5px; }
        .field[hidden] { display:none; }
        .label { font-size:13px; font-weight:600; color:var(--brown); }
        .input, .select { width:100%; height:42px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; padding:0 14px; }
        .input:focus, .select:focus { border-color:var(--brand); outline:none; box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .hint { font-size:12px; color:var(--muted); }
        .opciones { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .opcion { display:flex; align-items:flex-start; gap:10px; padding:14px; border:1.5px solid var(--border); border-radius:12px; cursor:pointer; }
        .opcion input { width:18px; height:18px; margin-top:2px; accent-color:var(--brand); }
        .opcion:has(input:checked) { border-color:var(--brand); background:var(--brand-light); }
        .opcion strong { display:block; font-size:14px; color:var(--text); }
        .opcion span { font-size:12px; color:var(--muted); }
        .preview { position:sticky; top:80px; }
        .preview-img { width:100%; border-radius:12px; border:1px solid var(--border); background:var(--beige-light); min-height:300px; display:block; }
        .cargando { font-size:12px; color:var(--brand); font-weight:700; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; height:44px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; width:100%; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); }
        .alert-error { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; background:#fce8ee; color:#7a2039; border:1px solid #f0b3c3; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="alert-error">
                    <strong>Revisa lo siguiente:</strong>
                    <ul style="margin:6px 0 0 18px; list-style:disc;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('festejos.store') }}" id="form-festejo">
                @csrf
                <div class="layout">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Versión</p>
                            <div class="opciones">
                                <label class="opcion">
                                    <input type="radio" name="variante" value="general" {{ $v('variante') === 'general' ? 'checked' : '' }}>
                                    <div><strong>🎊 General</strong><span>Con confeti, para toda la empresa</span></div>
                                </label>
                                <label class="opcion">
                                    <input type="radio" name="variante" value="turno" {{ $v('variante') === 'turno' ? 'checked' : '' }}>
                                    <div><strong>📋 Por turno</strong><span>Con la lista de los cumpleañeros del turno</span></div>
                                </label>
                            </div>

                            <div class="grid2">
                                <div class="field">
                                    <label class="label" for="mes">Cumpleañeros de</label>
                                    <input type="month" id="mes" name="mes" class="input" value="{{ $v('mes') }}" required>
                                </div>
                                <div class="field" id="campo-turno">
                                    <label class="label" for="turno">Turno</label>
                                    <select id="turno" name="turno" class="select">
                                        <option value="diurno" {{ $v('turno') === 'diurno' ? 'selected' : '' }}>Turno diurno</option>
                                        <option value="nocturno" {{ $v('turno') === 'nocturno' ? 'selected' : '' }}>Turno nocturno</option>
                                    </select>
                                </div>
                            </div>

                            <p class="card-title" style="margin-top:6px;">El festejo</p>
                            <div class="grid2">
                                <div class="field">
                                    <label class="label" for="fecha">Fecha</label>
                                    <input type="date" id="fecha" name="fecha" class="input" value="{{ $v('fecha') }}" min="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="field">
                                    <label class="label" for="hora">Hora</label>
                                    <input type="time" id="hora" name="hora" class="input" value="{{ $v('hora') }}" required>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="lugar">Lugar</label>
                                <input id="lugar" name="lugar" class="input" maxlength="60" value="{{ $v('lugar') }}" required>
                                <span class="hint">Queda como "Te esperamos a las 1:30 pm en <strong>el comedor</strong>".</span>
                            </div>
                            <div class="field" id="campo-nota">
                                <label class="label" for="nota">Nota</label>
                                <input id="nota" name="nota" class="input" maxlength="120" value="{{ $v('nota') }}">
                            </div>

                            <p class="card-title" style="margin-top:6px;">Publicación</p>
                            <div class="opciones">
                                <label class="opcion" style="padding:10px 12px;">
                                    <input type="radio" name="cuando" value="ahora" {{ old('cuando', 'ahora') === 'ahora' ? 'checked' : '' }}>
                                    <div><strong>Ahora</strong></div>
                                </label>
                                <label class="opcion" style="padding:10px 12px;">
                                    <input type="radio" name="cuando" value="programar" {{ old('cuando') === 'programar' ? 'checked' : '' }}>
                                    <div><strong>⏰ Programar</strong></div>
                                </label>
                            </div>
                            <div class="field" id="campo-publicar" hidden>
                                <label class="label" for="publicar_en">Publicar el</label>
                                <input type="datetime-local" id="publicar_en" name="publicar_en" class="input"
                                       value="{{ old('publicar_en') }}" min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                            </div>
                            <span class="hint">Se publica para todos, con push y un correo al grupo de toda la empresa, desde Recursos Humanos.</span>

                            <button type="submit" class="btn btn-primary" id="btn-publicar">🎉 Publicar invitación</button>
                        </div>
                    </div>

                    {{-- Vista previa --}}
                    <div class="card preview">
                        <div class="card-body">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <p class="card-title">Vista previa</p>
                                <span class="cargando" id="cargando" hidden>Generando…</span>
                            </div>
                            <img id="vista" class="preview-img" alt="Vista previa de la invitación">
                            <span class="hint" id="aviso-lista" hidden>La lista se llena sola con quienes cumplen años ese mes en ese turno.</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const form    = document.getElementById('form-festejo');
            const vista   = document.getElementById('vista');
            const aviso   = document.getElementById('cargando');
            const token   = form.querySelector('input[name=_token]').value;
            const $ = id => document.getElementById(id);
            const variante = () => form.querySelector('input[name=variante]:checked').value;
            let ultimaUrl = null, espera = null;

            // Lo que cambia según la versión y si se programa
            function aplicar() {
                const turno = variante() === 'turno';
                $('campo-turno').style.visibility = turno ? 'visible' : 'hidden';
                $('campo-nota').hidden = !turno;
                $('aviso-lista').hidden = !turno;

                const programar = form.querySelector('input[name=cuando]:checked').value === 'programar';
                $('campo-publicar').hidden = !programar;
                $('publicar_en').required = programar;
                $('btn-publicar').textContent = programar ? '⏰ Programar invitación' : '🎉 Publicar invitación';
            }

            // La vista previa la arma el servidor, con las mismas fuentes que la imagen final
            function actualizar() {
                clearTimeout(espera);
                espera = setTimeout(async () => {
                    aviso.hidden = false;
                    try {
                        const r = await fetch(@json(route('festejos.vista-previa')), {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'image/png' },
                            body: new FormData(form),
                        });
                        if (!r.ok) return; // faltan datos: se queda la vista anterior
                        if (ultimaUrl) URL.revokeObjectURL(ultimaUrl);
                        ultimaUrl = URL.createObjectURL(await r.blob());
                        vista.src = ultimaUrl;
                    } finally {
                        aviso.hidden = true;
                    }
                }, 600);
            }

            form.addEventListener('input', () => { aplicar(); actualizar(); });
            form.addEventListener('change', () => { aplicar(); actualizar(); });

            form.addEventListener('submit', e => {
                const programar = form.querySelector('input[name=cuando]:checked').value === 'programar';
                const msg = programar
                    ? 'La invitación se publicará sola en la fecha elegida, con push y correo a todos. ¿Continuar?'
                    : 'Se publicará ahora para todos, con push y correo al grupo. ¿Continuar?';
                if (!confirm(msg)) e.preventDefault();
            });

            aplicar();
            actualizar();
        })();
    </script>
</x-app-layout>