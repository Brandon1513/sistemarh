<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('comunicados.admin.index', ['estado' => 'por_revisar']) }}" style="color:#BBA4C0;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">🔎 Revisar comunicado</h2>
        </div>
    </x-slot>

    <style>
        :root { --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682; }
        .layout { display:grid; grid-template-columns:minmax(0,1fr) 320px; gap:20px; align-items:start; }
        @media(max-width:960px){ .layout { grid-template-columns:minmax(0,1fr); } }
        .card { background:#fff; border-radius:16px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; }
        .card-body { padding:24px 26px; display:flex; flex-direction:column; gap:14px; }
        .card-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); }
        .portada { width:100%; max-height:520px; object-fit:contain; background:var(--beige-light); display:block; cursor:zoom-in; }
        .chip { display:inline-flex; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; background:var(--brand-light); color:var(--brand); }
        .titulo { font-size:24px; font-weight:800; color:var(--text); line-height:1.25; }
        .meta { font-size:13px; color:var(--muted); }
        .contenido { font-size:15.5px; color:#3d2943; line-height:1.75; }
        .contenido p { margin:0 0 1em; }
        .dato { display:flex; justify-content:space-between; gap:10px; font-size:13.5px; padding:8px 0; border-bottom:1px solid #f5f0f7; }
        .dato:last-child { border-bottom:none; }
        .dato span:first-child { color:var(--muted); }
        .dato span:last-child { font-weight:600; color:var(--text); text-align:right; }
        .textarea { width:100%; border:1.5px solid var(--border); border-radius:9px; font-size:14px; padding:12px 14px; min-height:110px; resize:vertical; }
        .textarea:focus { border-color:var(--brand); outline:none; box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:7px; height:44px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; width:100%; }
        .btn-green { background:#2d7a4f; color:#fff; }
        .btn-rose { background:#fff; color:var(--rose); border:1.5px solid var(--rose); }
        .error { font-size:12px; color:var(--rose); }
        .galeria { display:grid; grid-template-columns:repeat(auto-fill, minmax(90px,1fr)); gap:8px; }
        .galeria img { width:100%; aspect-ratio:1; object-fit:cover; border-radius:8px; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="layout">
                {{-- Vista previa: tal como lo verán los empleados --}}
                <div class="card">
                    @if($comunicado->imagen_url)
                        <img src="{{ $comunicado->imagen_url }}" class="portada" alt="" onclick="window.open(this.src, '_blank')">
                    @endif
                    <div class="card-body">
                        <div><span class="chip">{{ $comunicado->tipo_nombre }}</span></div>
                        <h1 class="titulo">{{ $comunicado->titulo }}</h1>
                        <p class="meta">✍ {{ $comunicado->firma }}</p>
                        @if($comunicado->resumen)<p class="meta" style="font-style:italic;">{{ $comunicado->resumen }}</p>@endif
                        <div class="contenido">{{ \App\Support\TextoComunicado::html($comunicado->contenido) }}</div>
                        @if($comunicado->enlace)
                            <p><a href="{{ $comunicado->enlace }}" target="_blank" rel="noopener" style="color:var(--brand); font-weight:600;">🔗 {{ $comunicado->enlace }}</a></p>
                        @endif
                        @if($comunicado->imagenes->isNotEmpty())
                            <p class="card-title">Galería</p>
                            <div class="galeria">
                                @foreach($comunicado->imagenes as $foto)<img src="{{ $foto->url }}" alt="">@endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Datos y decisión --}}
                <div style="display:flex; flex-direction:column; gap:20px;">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Detalles</p>
                            <div>
                                <div class="dato"><span>Lo escribió</span><span>{{ $comunicado->autor?->name ?? '—' }}</span></div>
                                <div class="dato"><span>Va dirigido a</span><span>{{ $comunicado->etiqueta_destinos }}</span></div>
                                <div class="dato"><span>Le llegará a</span><span>{{ $alcance }} {{ $alcance === 1 ? 'persona' : 'personas' }}</span></div>
                                <div class="dato"><span>Enviado a revisión</span><span>{{ $comunicado->updated_at->locale('es')->diffForHumans() }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Tu decisión</p>

                            <form method="POST" action="{{ route('comunicados.admin.aprobar', $comunicado) }}"
                                  onsubmit="return confirm('Al aprobarlo se publica de inmediato para {{ $alcance }} personas. ¿Continuar?')">
                                @csrf
                                <button class="btn btn-green">✓ Aprobar y publicar</button>
                            </form>

                            <form method="POST" action="{{ route('comunicados.admin.devolver', $comunicado) }}" style="display:flex; flex-direction:column; gap:10px; margin-top:8px;">
                                @csrf
                                <textarea name="comentario" class="textarea" maxlength="2000" placeholder="¿Qué hay que corregir? Ej. La fecha del pago es el viernes 16, no el 15.">{{ old('comentario') }}</textarea>
                                @error('comentario')<span class="error">⚠ {{ $message }}</span>@enderror
                                <button class="btn btn-rose">↩ Devolver con comentarios</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>