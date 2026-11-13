@php
    $total = $leyeron->count() + $faltan->count();
    $porcentaje = $total ? round($leyeron->count() * 100 / $total) : 0;
    $desdeApp = $leyeron->where('canal', 'app')->count();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('comunicados.admin.index', ['estado' => 'publicado']) }}" style="color:#BBA4C0;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">👁 ¿Quién lo leyó?</h2>
        </div>
    </x-slot>

    <style>
        :root { --brand:#6A2C75; --brand-light:#f3eef5; --gold:#D6A644; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682; }
        .card { background:#fff; border-radius:16px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; }
        .resumen { padding:22px 26px; display:grid; grid-template-columns:minmax(0,1fr) auto; gap:20px; align-items:center; }
        @media(max-width:700px){ .resumen { grid-template-columns:minmax(0,1fr); } }
        .titulo { font-size:18px; font-weight:800; color:var(--text); }
        .sub { font-size:13px; color:var(--muted); margin-top:3px; }
        .barra { height:10px; border-radius:10px; background:var(--brand-light); overflow:hidden; margin-top:14px; }
        .barra > div { height:100%; background:linear-gradient(90deg, var(--brand), var(--gold)); border-radius:10px; }
        .cifras { display:flex; gap:22px; margin-top:10px; font-size:13px; color:var(--muted); flex-wrap:wrap; }
        .cifras strong { color:var(--text); font-size:15px; }
        .grande { font-size:42px; font-weight:800; color:var(--brand); line-height:1; text-align:right; }
        .grande small { display:block; font-size:12px; font-weight:600; color:var(--muted); margin-top:4px; }
        .toolbar { display:flex; gap:10px; align-items:center; justify-content:space-between; flex-wrap:wrap; padding:14px 26px; background:var(--beige-light); border-top:1px solid var(--border); border-bottom:1px solid var(--border); }
        .tabs { display:flex; gap:6px; }
        .tab { padding:8px 14px; border-radius:20px; font-size:13px; font-weight:700; border:1.5px solid var(--border); background:#fff; color:var(--muted); cursor:pointer; }
        .tab.activa { background:var(--brand); border-color:var(--brand); color:#fff; }
        .buscar { height:38px; min-width:220px; padding:0 12px; border:1.5px solid var(--border); border-radius:8px; font-size:13.5px; }
        .btn { display:inline-flex; align-items:center; gap:6px; height:38px; padding:0 14px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:#2d7a4f; color:#fff; }
        .fila { display:grid; grid-template-columns:36px minmax(0,1fr) auto; gap:12px; align-items:center; padding:11px 26px; border-bottom:1px solid #f5f0f7; }
        .fila:last-child { border-bottom:none; }
        .avatar { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; }
        .avatar.si { background:#e6f4ec; color:#1b6b38; }
        .avatar.no { background:#f0ecf3; color:var(--muted); }
        .nombre { font-size:14px; font-weight:700; color:var(--text); }
        .detalle { font-size:12px; color:var(--muted); }
        .cuando { font-size:12px; color:var(--muted); text-align:right; white-space:nowrap; }
        .vacio { padding:40px 26px; text-align:center; color:var(--muted); font-size:14px; }
        [hidden] { display:none !important; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="resumen">
                    <div>
                        <p class="titulo">{{ $comunicado->titulo }}</p>
                        <p class="sub">{{ $comunicado->tipo_nombre }} · 👥 {{ $comunicado->etiqueta_destinos }} · publicado {{ $comunicado->publicado_en?->locale('es')->isoFormat('D [de] MMMM, H:mm') }}</p>
                        <div class="barra"><div style="width: {{ $porcentaje }}%"></div></div>
                        <div class="cifras">
                            <span><strong>{{ $leyeron->count() }}</strong> lo leyeron</span>
                            <span><strong>{{ $faltan->count() }}</strong> faltan</span>
                            <span>📱 {{ $desdeApp }} desde la app · 💻 {{ $leyeron->count() - $desdeApp }} desde la web</span>
                        </div>
                    </div>
                    <div class="grande">{{ $porcentaje }}%<small>de {{ $total }} personas</small></div>
                </div>

                <div class="toolbar">
                    <div class="tabs">
                        <button type="button" class="tab activa" data-lista="faltan">Faltan ({{ $faltan->count() }})</button>
                        <button type="button" class="tab" data-lista="leyeron">Ya lo leyeron ({{ $leyeron->count() }})</button>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <input type="search" class="buscar" id="buscar" placeholder="Buscar persona, área o sede...">
                        <a href="{{ route('comunicados.admin.lecturas', [$comunicado, 'exportar' => 'csv']) }}" class="btn">⬇ Excel</a>
                    </div>
                </div>

                {{-- Los que faltan --}}
                <div data-panel="faltan">
                    @forelse($faltan as $u)
                        <div class="fila" data-texto="{{ Str::lower($u->name . ' ' . $u->departamento?->name . ' ' . $u->sede?->nombre) }}">
                            <div class="avatar no">{{ Str::upper(Str::substr($u->name, 0, 1)) }}</div>
                            <div style="min-width:0;">
                                <div class="nombre">{{ $u->name }}</div>
                                <div class="detalle">{{ collect([$u->departamento?->name, $u->sede?->nombre])->filter()->implode(' · ') }}</div>
                            </div>
                            <div class="cuando">Pendiente</div>
                        </div>
                    @empty
                        <div class="vacio">🎉 ¡Todos lo leyeron!</div>
                    @endforelse
                </div>

                {{-- Los que ya lo leyeron --}}
                <div data-panel="leyeron" hidden>
                    @forelse($leyeron as $l)
                        <div class="fila" data-texto="{{ Str::lower($l->user->name . ' ' . $l->user->departamento?->name . ' ' . $l->user->sede?->nombre) }}">
                            <div class="avatar si">✓</div>
                            <div style="min-width:0;">
                                <div class="nombre">{{ $l->user->name }}</div>
                                <div class="detalle">{{ collect([$l->user->departamento?->name, $l->user->sede?->nombre])->filter()->implode(' · ') }}</div>
                            </div>
                            <div class="cuando">
                                {{ $l->canal === 'app' ? '📱' : '💻' }} {{ $l->leido_en->locale('es')->isoFormat('D MMM, H:mm') }}
                            </div>
                        </div>
                    @empty
                        <div class="vacio">Todavía nadie lo ha abierto.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pestañas
        document.querySelectorAll('[data-lista]').forEach(tab => tab.addEventListener('click', () => {
            document.querySelectorAll('[data-lista]').forEach(t => t.classList.toggle('activa', t === tab));
            document.querySelectorAll('[data-panel]').forEach(p => p.hidden = p.dataset.panel !== tab.dataset.lista);
        }));

        // Buscador (filtra en las dos listas)
        document.getElementById('buscar').addEventListener('input', e => {
            const q = e.target.value.trim().toLowerCase();
            document.querySelectorAll('.fila[data-texto]').forEach(f => { f.hidden = q && !f.dataset.texto.includes(q); });
        });
    </script>
</x-app-layout>