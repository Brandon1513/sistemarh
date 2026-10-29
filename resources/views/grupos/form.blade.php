@php
    $editando = $grupo->exists;

    // Si el formulario regresó con errores, se conservan las personas que ya se habían agregado
    if (old('integrantes')) {
        $integrantes = \App\Models\User::with('sede')->whereIn('id', old('integrantes'))->orderBy('name')->get()
            ->map(fn ($u) => ['id' => $u->id, 'nombre' => $u->name, 'detalle' => collect([$u->puesto_empleado, $u->sede?->nombre])->filter()->implode(' · ')]);
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('grupos.index') }}" style="color:#BBA4C0;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $editando ? 'Editar grupo' : 'Nuevo grupo' }}</h2>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0;
            --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682;
        }
        .card { background:#fff; border-radius:16px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); }
        .card-body { padding:26px 28px; display:flex; flex-direction:column; gap:20px; }
        .footer { padding:20px 28px; background:var(--beige-light); border-top:1.5px solid var(--border); display:flex; justify-content:flex-end; gap:12px; border-radius:0 0 16px 16px; }
        .field { display:flex; flex-direction:column; gap:5px; }
        .label { font-size:13px; font-weight:600; color:var(--brown); }
        .req { color:var(--rose); }
        .input { width:100%; height:42px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; padding:0 14px; }
        .input:focus { border-color:var(--brand); outline:none; box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .hint { font-size:12px; color:var(--muted); }
        .error { font-size:12px; color:var(--rose); }
        .buscador { position:relative; }
        .resultados { position:absolute; left:0; right:0; top:calc(100% + 4px); background:#fff; border:1px solid var(--border); border-radius:10px; box-shadow:0 8px 24px rgba(61,21,69,.18); max-height:280px; overflow-y:auto; z-index:20; display:none; }
        .resultados.show { display:block; }
        .resultado { display:block; width:100%; text-align:left; padding:10px 14px; border:none; background:none; cursor:pointer; border-bottom:1px solid #f5f0f7; }
        .resultado:hover { background:var(--brand-light); }
        .resultado strong { display:block; font-size:13.5px; color:var(--text); }
        .resultado small { font-size:12px; color:var(--muted); }
        .resultado-vacio { padding:12px 14px; font-size:13px; color:var(--muted); }
        .lista { border:1px solid var(--border); border-radius:12px; overflow:hidden; }
        .lista-cabeza { display:flex; justify-content:space-between; align-items:center; padding:10px 14px; background:var(--beige-light); font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; }
        .integrante { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 14px; border-top:1px solid #f5f0f7; }
        .integrante strong { display:block; font-size:13.5px; color:var(--text); }
        .integrante small { font-size:12px; color:var(--muted); }
        .integrante button { border:none; background:#fce8ee; color:var(--rose); font-size:12px; font-weight:700; padding:5px 10px; border-radius:6px; cursor:pointer; }
        .vacio { padding:18px 14px; text-align:center; font-size:13px; color:var(--muted); border-top:1px solid #f5f0f7; }
        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ $editando ? route('grupos.update', $grupo) : route('grupos.store') }}" class="card" id="form-grupo">
                @csrf
                @if($editando) @method('PUT') @endif

                <div class="card-body">
                    <div class="field">
                        <label class="label" for="nombre">Nombre <span class="req">*</span></label>
                        <input id="nombre" name="nombre" class="input" maxlength="100" required
                               value="{{ old('nombre', $grupo->nombre) }}" placeholder="Ej. Comité de seguridad">
                        @error('nombre')<span class="error">⚠ {{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <label class="label" for="descripcion">Descripción</label>
                        <input id="descripcion" name="descripcion" class="input" maxlength="255"
                               value="{{ old('descripcion', $grupo->descripcion) }}" placeholder="Para qué es este grupo">
                    </div>
                    <div class="field">
                        <label class="label" for="correo_grupo">Correo del grupo de Microsoft 365 <span style="font-weight:400; color:var(--muted);">(opcional)</span></label>
                        <input id="correo_grupo" name="correo_grupo" type="email" class="input" maxlength="255"
                            value="{{ old('correo_grupo', $grupo->correo_grupo) }}" placeholder="comite@dasavena.com">
                        <span class="hint">Si lo pones, los comunicados que vayan solo a este grupo se mandan en un solo correo a esa dirección.</span>
                        @error('correo_grupo')<span class="error">⚠ {{ $message }}</span>@enderror
                    </div>

                    <div class="field">
                        <span class="label">Agregar personas</span>
                        <div class="buscador">
                            <input type="search" id="buscar-persona" class="input" autocomplete="off"
                                   placeholder="Escribe un nombre, correo o clave de empleado...">
                            <div class="resultados" id="resultados-personas"></div>
                        </div>
                    </div>

                    <div class="lista">
                        <div class="lista-cabeza">
                            <span>Integrantes</span>
                            <span id="total-integrantes">{{ count($integrantes) }}</span>
                        </div>
                        <div id="integrantes">
                            @foreach($integrantes as $persona)
                                <div class="integrante" data-id="{{ $persona['id'] }}">
                                    <div>
                                        <strong>{{ $persona['nombre'] }}</strong>
                                        <small>{{ $persona['detalle'] }}</small>
                                    </div>
                                    <input type="hidden" name="integrantes[]" value="{{ $persona['id'] }}">
                                    <button type="button" data-quitar>Quitar</button>
                                </div>
                            @endforeach
                        </div>
                        <div class="vacio" id="sin-integrantes" @if(count($integrantes)) hidden @endif>
                            Busca personas arriba para agregarlas al grupo.
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <a href="{{ route('grupos.index') }}" class="btn btn-ghost">Cancelar</a>
                    <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Crear grupo' }}</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const buscador   = document.getElementById('buscar-persona');
            const resultados = document.getElementById('resultados-personas');
            const lista      = document.getElementById('integrantes');
            const vacio      = document.getElementById('sin-integrantes');
            const total      = document.getElementById('total-integrantes');
            const escapar = t => String(t).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            function actualizarTotal() {
                const n = lista.querySelectorAll('.integrante').length;
                total.textContent = n;
                vacio.hidden = n > 0;
            }

            let espera = null;
            buscador.addEventListener('input', () => {
                clearTimeout(espera);
                const q = buscador.value.trim();
                if (q.length < 2) { resultados.classList.remove('show'); return; }

                espera = setTimeout(async () => {
                    const r = await fetch(@json(route('comunicados.admin.buscar-personas')) + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                    const personas = await r.json();
                    const yaEstan = new Set([...lista.querySelectorAll('[data-id]')].map(el => el.dataset.id));

                    resultados.innerHTML = personas.length
                        ? personas.map(p => `
                            <button type="button" class="resultado" data-id="${p.id}" data-nombre="${escapar(p.nombre)}" data-detalle="${escapar(p.detalle || '')}"
                                    ${yaEstan.has(String(p.id)) ? 'disabled style="opacity:.45"' : ''}>
                                <strong>${escapar(p.nombre)}</strong>
                                <small>${escapar(p.detalle || '')}${yaEstan.has(String(p.id)) ? ' · ya está en el grupo' : ''}</small>
                            </button>`).join('')
                        : '<div class="resultado-vacio">No se encontró a nadie.</div>';
                    resultados.classList.add('show');
                }, 300);
            });

            resultados.addEventListener('click', e => {
                const boton = e.target.closest('.resultado');
                if (!boton || boton.disabled) return;

                const fila = document.createElement('div');
                fila.className = 'integrante';
                fila.dataset.id = boton.dataset.id;
                fila.innerHTML = `
                    <div><strong>${escapar(boton.dataset.nombre)}</strong><small>${escapar(boton.dataset.detalle)}</small></div>
                    <input type="hidden" name="integrantes[]" value="${boton.dataset.id}">
                    <button type="button" data-quitar>Quitar</button>`;
                lista.prepend(fila);

                buscador.value = '';
                resultados.classList.remove('show');
                buscador.focus();
                actualizarTotal();
            });

            lista.addEventListener('click', e => {
                if (e.target.matches('[data-quitar]')) {
                    e.target.closest('.integrante').remove();
                    actualizarTotal();
                }
            });

            document.addEventListener('click', e => { if (!e.target.closest('.buscador')) resultados.classList.remove('show'); });
            buscador.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
        })();
    </script>
</x-app-layout>