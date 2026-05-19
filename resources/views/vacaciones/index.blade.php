<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h11m-7 4h7"/>
                </svg>
                Solicitudes de Vacaciones
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ $solicitudes->total() }} registros
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

        .card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }

        .filter-bar { background:var(--beige-light); border-bottom:1.5px solid var(--border); padding:18px 24px; display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
        .filter-group { display:flex; flex-direction:column; gap:5px; flex:1; min-width:160px; }
        .filter-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); }
        .filter-input,.filter-select { height:38px; padding:0 12px; border:1.5px solid var(--border); border-radius:8px; font-size:13.5px; color:var(--text); background:#fff; transition:border-color .2s,box-shadow .2s; outline:none; width:100%; }
        .filter-input:focus,.filter-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }

        .btn { display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; white-space:nowrap; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 12px rgba(106,44,117,.3); color:#fff; }
        .btn-outline  { background:#fff; color:var(--muted); border:1.5px solid var(--border); }
        .btn-outline:hover { border-color:var(--brand-mid); color:var(--brand); }
        .btn-gold   { background:var(--gold); color:#fff; }
        .btn-gold:hover { background:#bf922d; color:#fff; transform:translateY(-1px); }
        .btn-green  { background:#2d7a4f; color:#fff; }
        .btn-green:hover { background:#236040; color:#fff; }
        .btn-rose   { background:var(--rose); color:#fff; }
        .btn-rose:hover { background:#923d59; color:#fff; }
        .btn-danger { background:#dc2626; color:#fff; }
        .btn-danger:hover { background:#b91c1c; color:#fff; }
        .btn-sm { height:30px; padding:0 11px; font-size:12px; border-radius:6px; }

        .data-table { width:100%; border-collapse:collapse; }
        .data-table thead th { padding:12px 16px; background:var(--beige-light); border-bottom:1.5px solid var(--border); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); text-align:left; }
        .data-table tbody tr { border-bottom:1px solid #f0e6f5; transition:background .15s; }
        .data-table tbody tr:hover { background:#faf4fc; }
        .data-table tbody tr:last-child { border-bottom:none; }
        .data-table td { padding:13px 16px; font-size:14px; color:var(--text); vertical-align:middle; }

        .avatar-ph { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--brand),var(--brand-mid)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:700; flex-shrink:0; }
        .avatar-sm { width:34px; height:34px; border-radius:50%; object-fit:cover; border:2px solid var(--border); }

        /* Período */
        .period-range { display:flex; flex-direction:column; gap:2px; }
        .period-from  { font-size:13px; font-weight:600; color:var(--text); }
        .period-to    { font-size:11px; color:var(--muted); }

        /* Días badge */
        .days-pill { display:inline-flex; align-items:center; justify-content:center; min-width:36px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; }
        .days-few  { background:#e8f5ee; color:#1b6b38; }
        .days-mid  { background:var(--gold-pale); color:var(--brown); }
        .days-many { background:var(--brand-light); color:var(--brand); }

        /* Status */
        .badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .badge-pendiente { background:#fef9c3; color:#854d0e; }
        .badge-aprobado  { background:#e8f5ee; color:#1b6b38; }
        .badge-rechazado { background:#fce8ee; color:var(--rose); }
        .badge-dot { width:6px; height:6px; border-radius:50%; }
        .badge-pendiente .badge-dot { background:#d97706; }
        .badge-aprobado  .badge-dot { background:#2d7a4f; }
        .badge-rechazado .badge-dot { background:var(--rose); }

        .alert { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; display:flex; align-items:center; gap:10px; }
        .alert-success { background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }
        .alert-error   { background:#fce8ee; color:#7a2039; border:1px solid #f0b3c3; }

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
            @if(session('error'))
                <div class="alert alert-error">
                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">

                {{-- Filtros --}}
                <form method="GET" action="{{ route('vacaciones.index') }}">
                    <div class="filter-bar">
                        <div class="filter-group" style="flex:2; min-width:200px;">
                            <span class="filter-label">Buscar empleado</span>
                            <div style="position:relative;">
                                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#BBA4C0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                                </svg>
                                <input type="text" name="search" placeholder="Nombre del empleado..." value="{{ request('search') }}" class="filter-input" style="padding-left:34px;">
                            </div>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Estado</span>
                            <select name="estado" class="filter-select">
                                <option value="">Todos</option>
                                <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                                <option value="aprobado"   {{ request('estado') === 'aprobado'   ? 'selected' : '' }}>Aprobado</option>
                                <option value="rechazado"  {{ request('estado') === 'rechazado'  ? 'selected' : '' }}>Rechazado</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Año / Período</span>
                            <input type="number" name="periodo" placeholder="{{ date('Y') }}" value="{{ request('periodo') }}" class="filter-input">
                        </div>
                        <div style="display:flex; gap:8px; align-items:flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Filtrar
                            </button>
                            @if(request()->hasAny(['search','estado','periodo']))
                                <a href="{{ route('vacaciones.index') }}" class="btn btn-outline">
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
                        Mostrando <strong style="color:var(--text);">{{ $solicitudes->firstItem() }}–{{ $solicitudes->lastItem() }}</strong>
                        de <strong style="color:var(--text);">{{ $solicitudes->total() }}</strong> resultados
                    </p>
                    <a href="{{ route('vacaciones.create') }}" class="btn btn-primary btn-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Solicitar Vacaciones
                    </a>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th class="hide-mobile">Período</th>
                                <th class="hide-mobile">Vacaciones</th>
                                <th class="hide-mobile">Días</th>
                                <th>Estado</th>
                                <th style="text-align:right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $solicitud)
                                @php
                                    $dias = $solicitud->dias_solicitados ?? 0;
                                    $daysClass = $dias <= 5 ? 'days-few' : ($dias <= 10 ? 'days-mid' : 'days-many');
                                @endphp
                                <tr>
                                    {{-- Empleado --}}
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            @if($solicitud->empleado && $solicitud->empleado->foto_perfil)
                                                <img src="{{ asset('storage/' . $solicitud->empleado->foto_perfil) }}" class="avatar-sm" alt="">
                                            @else
                                                <div class="avatar-ph">{{ strtoupper(substr($solicitud->empleado->name ?? 'N', 0, 1)) }}</div>
                                            @endif
                                            <div>
                                                <p style="font-weight:600; font-size:13px; line-height:1.3;">
                                                    {{ $solicitud->empleado->name ?? 'N/A' }}
                                                    @if(isset($solicitud->tipo) && $solicitud->tipo === 'espontanea')
                                                        <span class="badge-espontanea">📋 RH</span>
                                                    @endif
                                                </p>
                                                <p style="font-size:11px; color:var(--muted);">{{ $solicitud->departamento->name ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Período correspondiente --}}
                                    <td class="hide-mobile">
                                        <span style="font-weight:700; font-size:15px; color:var(--brand);">{{ $solicitud->periodo_correspondiente }}</span>
                                    </td>

                                    {{-- Fechas --}}
                                    <td class="hide-mobile">
                                        <div class="period-range">
                                            <span class="period-from">{{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y') }}</span>
                                            <span class="period-to">→ {{ \Carbon\Carbon::parse($solicitud->fecha_fin)->format('d/m/Y') }}</span>
                                        </div>
                                    </td>

                                    {{-- Días --}}
                                    <td class="hide-mobile">
                                        <span class="days-pill {{ $daysClass }}">{{ $dias }} días</span>
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        <span class="badge badge-{{ $solicitud->estado }}">
                                            <span class="badge-dot"></span>
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                    </td>

                                    {{-- Acciones --}}
                                    <td style="text-align:right;">
                                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px; flex-wrap:wrap;">
                                            <a href="{{ route('vacaciones.show', $solicitud->id) }}" class="btn btn-gold btn-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Ver
                                            </a>

                                            @if($solicitud->estado === 'pendiente')
                                                @if($solicitud->empleado_id === auth()->id())
                                                    <form action="{{ route('vacaciones.destroy', $solicitud->id) }}" method="POST" style="display:inline;"
                                                        onsubmit="return confirm('¿Seguro que deseas eliminar esta solicitud?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('vacaciones.aprobar', $solicitud->id) }}" class="btn btn-green btn-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Aprobar
                                                    </a>
                                                    <a href="{{ route('vacaciones.rechazar', $solicitud->id) }}" class="btn btn-rose btn-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Rechazar
                                                    </a>
                                                @endif
                                            @elseif($solicitud->estado === 'aprobado')
                                                <span style="font-size:12px; color:var(--muted); padding:0 8px;">✓ Aprobado</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 6h18M3 14h11m-7 4h7"/></svg>
                                        <p style="font-size:15px; font-weight:600; color:var(--text); margin-bottom:4px;">Sin solicitudes</p>
                                        <p style="font-size:13px;">No se encontraron solicitudes con los filtros aplicados.</p>
                                        <a href="{{ route('vacaciones.index') }}" class="btn btn-outline" style="margin-top:16px;">Limpiar filtros</a>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="padding:16px 24px; border-top:1px solid var(--border);">
                    {{ $solicitudes->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>