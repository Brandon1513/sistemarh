<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                Gestión de Noticias
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ $noticias->total() }} noticias
            </span>
        </div>
    </x-slot>

    <style>
        :root { --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0; --gold:#D6A644; --gold-pale:#faf3e0; --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682; }
        .card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }
        .filter-bar { background:var(--beige-light); border-bottom:1.5px solid var(--border); padding:16px 24px; display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
        .filter-group { display:flex; flex-direction:column; gap:5px; flex:1; min-width:180px; }
        .filter-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); }
        .filter-input { height:38px; padding:0 12px 0 34px; border:1.5px solid var(--border); border-radius:8px; font-size:13.5px; color:var(--text); background:#fff; transition:border-color .2s,box-shadow .2s; outline:none; width:100%; }
        .filter-input:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .btn { display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; white-space:nowrap; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 12px rgba(106,44,117,.3); color:#fff; }
        .btn-gold   { background:var(--gold); color:#fff; }
        .btn-gold:hover { background:#bf922d; color:#fff; transform:translateY(-1px); }
        .btn-rose   { background:var(--rose); color:#fff; }
        .btn-rose:hover { background:#923d59; color:#fff; }
        .btn-success{ background:#2d7a4f; color:#fff; }
        .btn-success:hover { background:#236040; color:#fff; }
        .btn-brown  { background:var(--brown); color:#fff; }
        .btn-brown:hover { background:#352718; color:#fff; }
        .btn-sm { height:30px; padding:0 11px; font-size:12px; border-radius:6px; }

        .news-table { width:100%; border-collapse:collapse; }
        .news-table thead th { padding:12px 16px; background:var(--beige-light); border-bottom:1.5px solid var(--border); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); text-align:left; }
        .news-table tbody tr { border-bottom:1px solid #f0e6f5; transition:background .15s; }
        .news-table tbody tr:hover { background:#faf4fc; }
        .news-table tbody tr:last-child { border-bottom:none; }
        .news-table td { padding:13px 16px; font-size:14px; color:var(--text); vertical-align:middle; }

        .news-thumb { width:52px; height:40px; object-fit:cover; border-radius:6px; border:1px solid var(--border); flex-shrink:0; }
        .news-title-cell { display:flex; align-items:center; gap:10px; }
        .news-title-text { font-weight:600; font-size:13.5px; }
        .news-date { font-size:12px; color:var(--muted); }

        .badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-active   { background:#e8f5ee; color:#1b6b38; }
        .badge-inactive { background:#fce8ee; color:var(--rose); }
        .badge-dot { width:6px; height:6px; border-radius:50%; display:inline-block; }
        .badge-active .badge-dot   { background:#2d7a4f; }
        .badge-inactive .badge-dot { background:var(--rose); }

        .alert-success { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; display:flex; align-items:center; gap:10px; background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }

        .empty-state { text-align:center; padding:56px 24px; color:var(--muted); }
        .empty-state svg { width:52px; height:52px; margin:0 auto 14px; opacity:.3; }

        @media(max-width:640px){ .hide-mobile{display:none;} }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert-success">
                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">

                {{-- Filtros --}}
                <form method="GET" action="{{ route('noticias.index') }}">
                    <div class="filter-bar">
                        <div class="filter-group">
                            <span class="filter-label">Buscar</span>
                            <div style="position:relative;">
                                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#BBA4C0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
                                <input type="text" name="search" placeholder="Buscar noticia..." value="{{ request('search') }}" class="filter-input">
                            </div>
                        </div>
                        <div style="display:flex; gap:8px; align-items:flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Buscar
                            </button>
                            @if(request('search'))
                                <a href="{{ route('noticias.index') }}" class="btn" style="background:#fff;color:var(--muted);border:1.5px solid var(--border);">✕ Limpiar</a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Sub-header --}}
                <div style="padding:14px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); flex-wrap:wrap; gap:10px;">
                    <p style="font-size:13px; color:var(--muted);">
                        Mostrando <strong style="color:var(--text);">{{ $noticias->firstItem() }}–{{ $noticias->lastItem() }}</strong>
                        de <strong style="color:var(--text);">{{ $noticias->total() }}</strong> resultados
                    </p>
                    <a href="{{ route('noticias.create') }}" class="btn btn-primary btn-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Nueva Noticia
                    </a>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    @forelse($noticias as $noticia)
                        <table class="news-table">
                            <thead>
                                <tr>
                                    <th>Noticia</th>
                                    <th class="hide-mobile">Autor</th>
                                    <th class="hide-mobile">Fecha</th>
                                    <th>Estado</th>
                                    <th style="text-align:right;">Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    @break
                    @empty
                    @endforelse

                    @if($noticias->isEmpty())
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            <p style="font-size:15px; font-weight:600; color:var(--text); margin-bottom:4px;">Sin noticias</p>
                            <p style="font-size:13px;">No se encontraron noticias con los filtros aplicados.</p>
                        </div>
                    @else
                        <table class="news-table">
                            <thead>
                                <tr>
                                    <th>Noticia</th>
                                    <th class="hide-mobile">Autor</th>
                                    <th class="hide-mobile">Fecha</th>
                                    <th>Estado</th>
                                    <th style="text-align:right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($noticias as $noticia)
                                    <tr>
                                        <td>
                                            <div class="news-title-cell">
                                                @if($noticia->imagen)
                                                    <img src="{{ asset('storage/' . $noticia->imagen) }}" class="news-thumb" alt="">
                                                @endif
                                                <span class="news-title-text">{{ Str::limit($noticia->titulo, 50) }}</span>
                                            </div>
                                        </td>
                                        <td class="hide-mobile" style="color:var(--muted); font-size:13px;">{{ $noticia->autor }}</td>
                                        <td class="hide-mobile">
                                            <span class="news-date">{{ \Carbon\Carbon::parse($noticia->fecha)->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $noticia->activo ? 'badge-active' : 'badge-inactive' }}">
                                                <span class="badge-dot"></span>
                                                {{ $noticia->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td style="text-align:right;">
                                            <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px; flex-wrap:wrap;">
                                                <a href="{{ route('noticias.edit', $noticia->id) }}" class="btn btn-gold btn-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Editar
                                                </a>
                                                <form action="{{ route('noticias.toggle', $noticia->id) }}" method="POST" style="display:inline;"
                                                    onsubmit="return confirm('¿{{ $noticia->activo ? 'Inactivar' : 'Activar' }} la noticia {{ addslashes($noticia->titulo) }}?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $noticia->activo ? 'btn-brown' : 'btn-success' }}">
                                                        {{ $noticia->activo ? 'Inactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                                <form action="{{ route('noticias.destroy', $noticia->id) }}" method="POST" style="display:inline;"
                                                    onsubmit="return confirm('¿Eliminar la noticia {{ addslashes($noticia->titulo) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-rose btn-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div style="padding:16px 24px; border-top:1px solid var(--border);">
                    {{ $noticias->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmToggle(action, titulo) {
            return confirm(`¿Deseas ${action} la noticia "${titulo}"?`);
        }
    </script>
</x-app-layout>