<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Períodos de Vacaciones
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ $periodos->total() }} registros
            </span>
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

        .vac-card {
            background:#fff;
            border-radius:16px;
            box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07);
            overflow:hidden; border:1px solid var(--border);
        }

        .filter-bar { background:var(--beige-light); border-bottom:1.5px solid var(--border); padding:18px 24px; display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
        .filter-group { display:flex; flex-direction:column; gap:5px; flex:1; min-width:180px; }
        .filter-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); }

        .filter-input,.filter-select {
            height:38px; padding:0 12px; border:1.5px solid var(--border); border-radius:8px;
            font-size:13.5px; color:var(--text); background:#fff;
            transition:border-color .2s,box-shadow .2s; outline:none; width:100%;
        }
        .filter-input:focus,.filter-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }

        .btn { display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; white-space:nowrap; text-decoration:none; }
        .btn-primary  { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 12px rgba(106,44,117,.3); color:#fff; }
        .btn-outline  { background:#fff; color:var(--muted); border:1.5px solid var(--border); }
        .btn-outline:hover { border-color:var(--brand-mid); color:var(--brand); }
        .btn-gold     { background:var(--gold); color:#fff; }
        .btn-gold:hover { background:#bf922d; transform:translateY(-1px); color:#fff; }
        .btn-green    { background:#2d7a4f; color:#fff; }
        .btn-green:hover { background:#236040; transform:translateY(-1px); color:#fff; }
        .btn-rose     { background:var(--rose); color:#fff; }
        .btn-rose:hover { background:#923d59; color:#fff; }
        .btn-success  { background:#2d7a4f; color:#fff; }
        .btn-success:hover { background:#236040; color:#fff; }
        .btn-sm { height:30px; padding:0 11px; font-size:12px; border-radius:6px; }

        .vac-table { width:100%; border-collapse:collapse; }
        .vac-table thead th { padding:12px 16px; background:var(--beige-light); border-bottom:1.5px solid var(--border); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); text-align:left; }
        .vac-table tbody tr { border-bottom:1px solid #f0e6f5; transition:background .15s; }
        .vac-table tbody tr:hover { background:#faf4fc; }
        .vac-table tbody tr:last-child { border-bottom:none; }
        .vac-table td { padding:13px 16px; font-size:14px; color:var(--text); vertical-align:middle; }

        /* Avatar inline */
        .avatar-sm { width:34px; height:34px; border-radius:50%; object-fit:cover; border:2px solid var(--border); }
        .avatar-placeholder-sm { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--brand),var(--brand-mid)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:700; flex-shrink:0; }

        /* Días pill */
        .days-pill { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .days-ok   { background:#e8f5ee; color:#1b6b38; }
        .days-low  { background:var(--gold-pale); color:var(--brown); }
        .days-none { background:#fce8ee; color:var(--rose); }

        /* Badge activo */
        .badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-active   { background:#e8f5ee; color:#1b6b38; }
        .badge-inactive { background:#fce8ee; color:var(--rose); }
        .badge-dot { width:6px; height:6px; border-radius:50%; }
        .badge-active .badge-dot   { background:#2d7a4f; }
        .badge-inactive .badge-dot { background:var(--rose); }

        .alert { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }

        .empty-state { text-align:center; padding:56px 24px; color:var(--muted); }
        .empty-state svg { width:52px; height:52px; margin:0 auto 14px; opacity:.3; }

        @media(max-width:640px){ .hide-mobile{display:none;} .filter-bar{padding:14px;} }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert alert-success">
                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="vac-card">

                {{-- Filtros --}}
                <form method="GET" action="{{ route('periodos.index') }}">
                    <div class="filter-bar">
                        <div class="filter-group" style="flex:2; min-width:220px;">
                            <span class="filter-label">Buscar</span>
                            <div style="position:relative;">
                                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#BBA4C0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                                </svg>
                                <input type="text" name="search" placeholder="Nombre o clave del empleado..." value="{{ request('search') }}" class="filter-input" style="padding-left:34px;">
                            </div>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Año</span>
                            <input type="number" name="anio" placeholder="Ej. 2024" value="{{ request('anio') }}" class="filter-input">
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Estado</span>
                            <select name="estado" class="filter-select">
                                <option value="">Todos</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div style="display:flex; gap:8px; align-items:flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Filtrar
                            </button>
                            @if(request()->hasAny(['search','anio','estado']))
                                <a href="{{ route('periodos.index') }}" class="btn btn-outline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Sub-header --}}
                <div style="padding:14px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); background:#fff; flex-wrap:wrap; gap:10px;">
                    <p style="font-size:13px; color:var(--muted);">
                        Mostrando <strong style="color:var(--text);">{{ $periodos->firstItem() }}–{{ $periodos->lastItem() }}</strong>
                        de <strong style="color:var(--text);">{{ $periodos->total() }}</strong> resultados
                    </p>
                    <div style="display:flex; gap:8px;">
                        <a href="{{ route('periodos.export') }}" class="btn btn-green btn-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Excel
                        </a>
                        <a href="{{ route('periodos.create') }}" class="btn btn-primary btn-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Nuevo Período
                        </a>
                    </div>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="vac-table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th class="hide-mobile">Año</th>
                                <th class="hide-mobile">Días Corresponden</th>
                                <th class="hide-mobile">Días Disponibles</th>
                                <th>Estado</th>
                                <th style="text-align:right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodos as $periodo)
                                @php
                                    $disp = $periodo->dias_disponibles ?? 0;
                                    $corr = $periodo->dias_corresponden ?? 0;
                                    $pct  = $corr > 0 ? ($disp / $corr) : 0;
                                    $daysClass = $pct > 0.5 ? 'days-ok' : ($pct > 0 ? 'days-low' : 'days-none');
                                @endphp
                                <tr>
                                    {{-- Empleado --}}
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            @if($periodo->empleado && $periodo->empleado->foto_perfil)
                                                <img src="{{ asset('storage/' . $periodo->empleado->foto_perfil) }}" class="avatar-sm" alt="">
                                            @else
                                                <div class="avatar-placeholder-sm">{{ strtoupper(substr($periodo->empleado->name ?? 'N', 0, 1)) }}</div>
                                            @endif
                                            <div>
                                                <p style="font-weight:600; font-size:14px; line-height:1.3;">{{ $periodo->empleado->name ?? 'N/A' }}</p>
                                                <p style="font-size:12px; color:var(--muted);">{{ $periodo->empleado->clave_empleado ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Año --}}
                                    <td class="hide-mobile">
                                        <span style="font-weight:700; font-size:15px; color:var(--brand);">{{ $periodo->anio }}</span>
                                    </td>

                                    {{-- Días corresponden --}}
                                    <td class="hide-mobile">
                                        <span style="font-size:14px; font-weight:600; color:var(--brown);">{{ $corr }} días</span>
                                    </td>

                                    {{-- Días disponibles con barra --}}
                                    <td class="hide-mobile">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <span class="days-pill {{ $daysClass }}">{{ $disp }} días</span>
                                            @if($corr > 0)
                                                <div style="flex:1; max-width:70px; height:5px; background:#f0e6f5; border-radius:3px; overflow:hidden;">
                                                    <div style="height:100%; width:{{ min(100, $pct * 100) }}%; background:{{ $pct > 0.5 ? '#2d7a4f' : ($pct > 0 ? '#D6A644' : '#AA4969') }}; border-radius:3px; transition:width .3s;"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        <span class="badge {{ $periodo->activo ? 'badge-active' : 'badge-inactive' }}">
                                            <span class="badge-dot"></span>
                                            {{ $periodo->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>

                                    {{-- Acciones --}}
                                    <td style="text-align:right;">
                                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                                            <a href="{{ route('periodos.edit', $periodo->id) }}" class="btn btn-gold btn-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Editar
                                            </a>
                                            <form action="{{ route('periodos_vacaciones.toggle-activo', $periodo->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $periodo->activo ? 'btn-rose' : 'btn-success' }}">
                                                    @if($periodo->activo)
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                        Inactivar
                                                    @else
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Activar
                                                    @endif
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p style="font-size:15px; font-weight:600; color:var(--text); margin-bottom:4px;">Sin resultados</p>
                                        <p style="font-size:13px;">No se encontraron períodos con los filtros aplicados.</p>
                                        <a href="{{ route('periodos.index') }}" class="btn btn-outline" style="margin-top:16px;">Limpiar filtros</a>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="padding:16px 24px; border-top:1px solid var(--border);">
                    {{ $periodos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>