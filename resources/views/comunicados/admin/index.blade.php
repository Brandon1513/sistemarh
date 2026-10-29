@php
    use App\Models\Comunicado;

    $estilosEstado = [
        'borrador'    => ['#f0ecf3', '#7a6682'],
        'en_revision' => ['#faf3e0', '#8a6a1f'],
        'devuelto'    => ['#fce8ee', '#AA4969'],
        'programado'  => ['#e0eaff', '#2b4c9b'],
        'publicado'   => ['#e6f4ec', '#1b6b38'],
        'archivado'   => ['#f0ecf3', '#7a6682'],
    ];
    $iconosTipo = ['noticia' => '📰', 'red_social' => '📱', 'aviso' => '📢', 'cumpleanos' => '🎂'];
    $viendoPorRevisar = request('estado') === 'por_revisar';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <span style="font-size:20px;">📣</span> Comunicados
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ $comunicados->total() }} en total
            </span>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0;
            --gold:#D6A644; --gold-light:#EED39B; --gold-pale:#faf3e0; --brown:#473524; --rose:#AA4969;
            --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682;
        }
        .card { background:#fff; border-radius:16px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; }
        .toolbar { padding:18px 24px; background:var(--beige-light); border-bottom:1.5px solid var(--border); display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; justify-content:space-between; }
        .filtros { display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end; }
        .f-label { display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); margin-bottom:5px; }
        .f-input { height:38px; padding:0 12px; border:1.5px solid var(--border); border-radius:8px; font-size:13.5px; color:var(--text); background:#fff; min-width:170px; }
        .btn { display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:all .15s; white-space:nowrap; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); color:#fff; }
        .btn-outline { background:#fff; color:var(--muted); border:1.5px solid var(--border); }
        .btn-sm { height:30px; padding:0 11px; font-size:12px; border-radius:6px; }
        .btn-gold { background:var(--gold); color:#fff; }
        .btn-green { background:#2d7a4f; color:#fff; }
        .btn-gray { background:#f0ecf3; color:var(--muted); }
        .btn-rose { background:var(--rose); color:#fff; }

        .tabs { display:flex; gap:4px; padding:0 24px; border-bottom:1px solid var(--border); overflow-x:auto; }
        .tab { padding:12px 14px; font-size:13px; font-weight:600; color:var(--muted); text-decoration:none; border-bottom:2.5px solid transparent; white-space:nowrap; }
        .tab:hover { color:var(--brand); }
        .tab.active { color:var(--brand); border-bottom-color:var(--brand); }
        .tab .n { margin-left:5px; font-size:11px; background:var(--brand-light); color:var(--brand); padding:1px 7px; border-radius:20px; }
        .tab-revisar { color:#8a6a1f; }
        .tab-revisar .n { background:var(--gold); color:var(--brown); }
        .tab-revisar.active { color:#8a6a1f; border-bottom-color:var(--gold); }
        .separador-tabs { width:1px; background:var(--border); margin:10px 6px; }

        .fila { display:grid; grid-template-columns:72px minmax(0,1fr) auto; gap:16px; align-items:center; padding:14px 24px; border-bottom:1px solid #f0e6f5; }
        .fila:last-child { border-bottom:none; }
        .fila:hover { background:#faf4fc; }
        .thumb { width:72px; height:56px; border-radius:10px; object-fit:cover; background:var(--brand-light); display:flex; align-items:center; justify-content:center; font-size:22px; }
        .titulo { font-size:14.5px; font-weight:700; color:var(--text); line-height:1.3; }
        .meta { display:flex; flex-wrap:wrap; gap:6px 12px; align-items:center; margin-top:5px; font-size:12px; color:var(--muted); }
        .chip { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:20px; font-size:11.5px; font-weight:700; }
        .acciones { display:flex; gap:6px; align-items:center; flex-wrap:wrap; justify-content:flex-end; }
        @media(max-width:768px){ .fila { grid-template-columns:56px minmax(0,1fr); } .thumb { width:56px; height:48px; } .acciones { grid-column:1 / -1; justify-content:flex-start; } }

        .nuevo-menu { position:relative; }
        .nuevo-panel { position:absolute; right:0; top:calc(100% + 6px); min-width:220px; background:#fff; border:1px solid var(--border); border-radius:12px; box-shadow:0 8px 32px rgba(61,21,69,.2); padding:6px; display:none; z-index:20; }
        .nuevo-menu.open .nuevo-panel { display:block; }
        .nuevo-item { display:flex; align-items:center; gap:10px; padding:9px 12px; border-radius:8px; font-size:13.5px; font-weight:500; color:var(--text); text-decoration:none; }
        .nuevo-item:hover { background:var(--brand-light); color:var(--brand); }

        .alert { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; }
        .alert-success { background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }
        .alert-warning { background:#faf3e0; color:#7a5a12; border:1px solid #EED39B; }
        .aviso-revision { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:14px 18px; border-radius:12px; margin-bottom:16px; background:var(--gold-pale); border:1px solid var(--gold-light); color:#7a5a12; font-size:14px; }
        .vacio { text-align:center; padding:56px 24px; color:var(--muted); }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">⚠ {{ session('warning') }}</div>
            @endif

            {{-- Recordatorio para el revisor, si tiene pendientes y no está viendo esa pestaña --}}
            @if($porRevisar > 0 && ! $viendoPorRevisar)
                <div class="aviso-revision">
                    <span>🔎 Tienes <strong>{{ $porRevisar }}</strong> {{ $porRevisar === 1 ? 'comunicado' : 'comunicados' }} esperando tu revisión.</span>
                    <a href="{{ route('comunicados.admin.index', ['estado' => 'por_revisar']) }}" class="btn btn-gold btn-sm">Revisar ahora</a>
                </div>
            @endif

            <div class="card">
                {{-- Filtros y botón de nuevo --}}
                <form method="GET" class="toolbar">
                    <input type="hidden" name="estado" value="{{ request('estado') }}">
                    <div class="filtros">
                        <div>
                            <label class="f-label" for="buscar">Buscar</label>
                            <input id="buscar" name="buscar" class="f-input" value="{{ request('buscar') }}" placeholder="Título...">
                        </div>
                        @if(count($tipos) > 1 && ! $viendoPorRevisar)
                            <div>
                                <label class="f-label" for="tipo">Tipo</label>
                                <select id="tipo" name="tipo" class="f-input" onchange="this.form.submit()">
                                    <option value="">Todos</option>
                                    @foreach($tipos as $t)
                                        <option value="{{ $t }}" {{ request('tipo') === $t ? 'selected' : '' }}>{{ Comunicado::TIPOS[$t] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-outline">Filtrar</button>
                    </div>

                    <div class="nuevo-menu" id="nuevoMenu">
                        @if(count($tipos) === 1)
                            <a href="{{ route('comunicados.admin.create', ['tipo' => $tipos[0]]) }}" class="btn btn-primary">＋ Nuevo {{ Str::lower(Comunicado::TIPOS[$tipos[0]]) }}</a>
                        @else
                            <button type="button" class="btn btn-primary" onclick="this.parentElement.classList.toggle('open')">＋ Nuevo comunicado ▾</button>
                            <div class="nuevo-panel">
                                @foreach($tipos as $t)
                                    <a href="{{ route('comunicados.admin.create', ['tipo' => $t]) }}" class="nuevo-item">
                                        <span>{{ $iconosTipo[$t] }}</span> {{ Comunicado::TIPOS[$t] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </form>

                {{-- Pestañas por estado --}}
                <nav class="tabs">
                    @if($porRevisar > 0 || $viendoPorRevisar)
                        <a href="{{ route('comunicados.admin.index', ['estado' => 'por_revisar']) }}"
                           class="tab tab-revisar {{ $viendoPorRevisar ? 'active' : '' }}">
                            🔎 Por revisar <span class="n">{{ $porRevisar }}</span>
                        </a>
                        <span class="separador-tabs"></span>
                    @endif

                    @foreach(['' => 'Todos', 'borrador' => 'Borradores', 'en_revision' => 'En revisión', 'devuelto' => 'Devueltos', 'programado' => 'Programados', 'publicado' => 'Publicados', 'archivado' => 'Archivados'] as $valor => $nombre)
                        @php $cuantos = $valor === '' ? $conteos->sum() : ($conteos[$valor] ?? 0); @endphp
                        {{-- Estas pestañas solo aparecen si hay alguno, para no llenar de pestañas vacías --}}
                        @continue(in_array($valor, ['en_revision', 'devuelto', 'programado']) && ! $cuantos && request('estado') !== $valor)
                        <a href="{{ route('comunicados.admin.index', array_filter(['estado' => $valor ?: null, 'tipo' => request('tipo'), 'buscar' => request('buscar')])) }}"
                           class="tab {{ ! $viendoPorRevisar && (string) request('estado') === $valor ? 'active' : '' }}">
                            {{ $nombre }}
                            <span class="n">{{ $cuantos }}</span>
                        </a>
                    @endforeach
                </nav>

                {{-- Lista --}}
                @forelse($comunicados as $c)
                    @php [$fondo, $color] = $estilosEstado[$c->estado] ?? ['#f0ecf3', '#7a6682']; @endphp
                    <div class="fila">
                        @if($c->imagen_url)
                            <img src="{{ $c->imagen_url }}" class="thumb" alt="" loading="lazy">
                        @else
                            <div class="thumb">{{ $iconosTipo[$c->tipo] ?? '📣' }}</div>
                        @endif

                        <div style="min-width:0;">
                            <div class="titulo">
                                @if($c->fijado)<span title="Fijado arriba">📌</span>@endif
                                {{ $c->titulo }}
                            </div>
                            <div class="meta">
                                <span class="chip" style="background:{{ $fondo }}; color:{{ $color }};">{{ $c->estado_nombre }}</span>
                                <span>{{ $iconosTipo[$c->tipo] ?? '' }} {{ $c->tipo_nombre }}@if($c->categoria) · {{ Comunicado::CATEGORIAS_AVISO[$c->categoria] ?? $c->categoria }}@endif</span>
                                <span title="Destinatarios">👥 {{ $c->etiqueta_destinos }}</span>

                                @if($viendoPorRevisar)
                                    <span>✍ Lo escribió {{ $c->autor?->name ?? '—' }}</span>
                                @else
                                    <span>✍ {{ $c->firma ?: 'Sin firma' }}</span>
                                @endif

                                @if($c->estado === 'en_revision' && ! $viendoPorRevisar)
                                    <span>🔎 Con {{ $c->revisor?->name ?? 'RH' }}</span>
                                @endif
                                @if($c->estado === 'devuelto')
                                    <span style="color:var(--rose); font-weight:600;">↩ Con comentarios de {{ $c->revisor?->name ?? 'RH' }}</span>
                                @endif

                                @if($c->estado === 'programado' && $c->publicar_en)
                                    <span style="color:#2b4c9b; font-weight:600;">⏰ Se publica el {{ $c->publicar_en->locale('es')->isoFormat('D MMM, H:mm') }}</span>
                                @elseif($c->publicado_en)
                                    <span>📅 {{ $c->publicado_en->locale('es')->isoFormat('D MMM YYYY, H:mm') }}</span>
                                @else
                                    <span>Editado {{ $c->updated_at->locale('es')->diffForHumans() }}</span>
                                @endif

                                @if($c->imagenes_count)<span>🖼 {{ $c->imagenes_count }}</span>@endif
                                @unless($c->enviar_push)<span title="Sin notificación push">🔕</span>@endunless
                            </div>
                        </div>

                        <div class="acciones">
                            {{-- El revisor: revisar --}}
                            @can('review', $c)
                                <a href="{{ route('comunicados.admin.revisar', $c) }}" class="btn btn-gold btn-sm">🔎 Revisar</a>
                            @endcan

                            {{-- El autor, mientras está en revisión: retirarlo --}}
                            @can('retirar', $c)
                                <form method="POST" action="{{ route('comunicados.admin.retirar', $c) }}"
                                      onsubmit="return confirm('¿Retirar «{{ addslashes($c->titulo) }}» de la revisión? Regresa a borrador.')">
                                    @csrf
                                    <button class="btn btn-gray btn-sm">Retirar de revisión</button>
                                </form>
                            @endcan

                            @can('update', $c)
                                <a href="{{ route('comunicados.admin.edit', $c) }}" class="btn btn-gold btn-sm">
                                    {{ $c->estado === 'devuelto' ? 'Corregir' : 'Editar' }}
                                </a>

                                @if(in_array($c->estado, ['borrador', 'archivado', 'programado']))
                                    <form method="POST" action="{{ route('comunicados.admin.publicar', $c) }}"
                                          onsubmit="return confirm('{{ $c->estado === 'programado' ? '¿Publicar «' . addslashes($c->titulo) . '» ahora, antes de su hora?' : '¿Publicar «' . addslashes($c->titulo) . '»? Lo verán sus destinatarios.' }}')">
                                        @csrf
                                        <button class="btn btn-green btn-sm">{{ $c->estado === 'programado' ? 'Publicar ya' : 'Publicar' }}</button>
                                    </form>
                                @endif

                                @if($c->estado === 'publicado')
                                    <form method="POST" action="{{ route('comunicados.admin.archivar', $c) }}"
                                          onsubmit="return confirm('¿Archivar «{{ addslashes($c->titulo) }}»? Dejará de verse, pero no se borra.')">
                                        @csrf
                                        <button class="btn btn-gray btn-sm">Archivar</button>
                                    </form>
                                @endif
                            @endcan

                            @can('delete', $c)
                                @if($c->estado !== 'en_revision')
                                    <form method="POST" action="{{ route('comunicados.admin.destroy', $c) }}"
                                          onsubmit="return confirm('¿Eliminar «{{ addslashes($c->titulo) }}»?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-rose btn-sm">Eliminar</button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="vacio">
                        @if($viendoPorRevisar)
                            <div style="font-size:40px; margin-bottom:8px;">✅</div>
                            <p style="font-weight:700; color:var(--text);">No tienes nada por revisar</p>
                            <p style="font-size:13px;">Cuando alguien te pida revisar un comunicado, aparecerá aquí y te llegará un correo.</p>
                        @else
                            <div style="font-size:40px; margin-bottom:8px;">📭</div>
                            <p style="font-weight:700; color:var(--text);">No hay comunicados aquí</p>
                            <p style="font-size:13px;">Cambia el filtro o crea uno nuevo.</p>
                        @endif
                    </div>
                @endforelse

                @if($comunicados->hasPages())
                    <div style="padding:16px 24px; border-top:1px solid var(--border);">{{ $comunicados->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Cierra el menú de "Nuevo" al hacer clic fuera
        document.addEventListener('click', e => {
            const menu = document.getElementById('nuevoMenu');
            if (menu && !menu.contains(e.target)) menu.classList.remove('open');
        });
    </script>
</x-app-layout>