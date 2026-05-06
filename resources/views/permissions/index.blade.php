<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Permisos Solicitados
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ $permissions->total() }} registros
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

        .perm-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }

        .filter-bar { background:var(--beige-light); border-bottom:1.5px solid var(--border); padding:18px 24px; display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
        .filter-group { display:flex; flex-direction:column; gap:5px; flex:1; min-width:160px; }
        .filter-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); }
        .filter-input,.filter-select { height:38px; padding:0 12px; border:1.5px solid var(--border); border-radius:8px; font-size:13.5px; color:var(--text); background:#fff; transition:border-color .2s,box-shadow .2s; outline:none; width:100%; }
        .filter-input:focus,.filter-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }

        .btn { display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; white-space:nowrap; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 12px rgba(106,44,117,.3); color:#fff; }
        .btn-outline { background:#fff; color:var(--muted); border:1.5px solid var(--border); }
        .btn-outline:hover { border-color:var(--brand-mid); color:var(--brand); }
        .btn-gold    { background:var(--gold); color:#fff; }
        .btn-gold:hover { background:#bf922d; color:#fff; transform:translateY(-1px); }
        .btn-green   { background:#2d7a4f; color:#fff; }
        .btn-green:hover { background:#236040; color:#fff; }
        .btn-rose    { background:var(--rose); color:#fff; }
        .btn-rose:hover { background:#923d59; color:#fff; }
        .btn-sm { height:30px; padding:0 11px; font-size:12px; border-radius:6px; }

        .perm-table { width:100%; border-collapse:collapse; }
        .perm-table thead th { padding:12px 16px; background:var(--beige-light); border-bottom:1.5px solid var(--border); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); text-align:left; }
        .perm-table tbody tr { border-bottom:1px solid #f0e6f5; transition:background .15s; }
        .perm-table tbody tr:hover { background:#faf4fc; }
        .perm-table tbody tr:last-child { border-bottom:none; }
        .perm-table td { padding:13px 16px; font-size:14px; color:var(--text); vertical-align:middle; }

        /* Avatar */
        .avatar-sm { width:34px; height:34px; border-radius:50%; object-fit:cover; border:2px solid var(--border); }
        .avatar-ph  { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--brand),var(--brand-mid)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:700; flex-shrink:0; }

        /* Motivo truncado con tooltip */
        .motivo-cell { max-width:220px; }
        .motivo-text { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; font-size:13px; line-height:1.45; color:var(--muted); cursor:default; }

        /* Tipo entrada/salida */
        .tipo-tag { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:6px; font-size:11px; font-weight:700; }
        .tipo-entrada { background:#e0eaff; color:#1d4ed8; }
        .tipo-salida  { background:var(--gold-pale); color:var(--brown); }

        /* Status badge */
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

            <div class="perm-card">

                {{-- Filtros --}}
                <form method="GET" action="{{ route('permissions.index') }}">
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
                            <select name="status" class="filter-select">
                                <option value="">Todos</option>
                                <option value="pendiente"  {{ request('status') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                                <option value="aprobado"   {{ request('status') === 'aprobado'   ? 'selected' : '' }}>Aprobado</option>
                                <option value="rechazado"  {{ request('status') === 'rechazado'  ? 'selected' : '' }}>Rechazado</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Tipo</span>
                            <select name="tipo" class="filter-select">
                                <option value="">Todos</option>
                                <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="salida"  {{ request('tipo') === 'salida'  ? 'selected' : '' }}>Salida</option>
                            </select>
                        </div>
                        <div style="display:flex; gap:8px; align-items:flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Filtrar
                            </button>
                            @if(request()->hasAny(['search','status','tipo']))
                                <a href="{{ route('permissions.index') }}" class="btn btn-outline">
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
                        Mostrando <strong style="color:var(--text);">{{ $permissions->firstItem() }}–{{ $permissions->lastItem() }}</strong>
                        de <strong style="color:var(--text);">{{ $permissions->total() }}</strong> resultados
                    </p>
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Solicitar Permiso
                    </a>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="perm-table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th class="hide-mobile">Tipo</th>
                                <th class="hide-mobile">Fecha</th>
                                <th>Motivo</th>
                                <th>Estado</th>
                                <th style="text-align:right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $permission)
                                <tr>
                                    {{-- Empleado --}}
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            @if($permission->user && $permission->user->foto_perfil)
                                                <img src="{{ asset('storage/' . $permission->user->foto_perfil) }}" class="avatar-sm" alt="">
                                            @else
                                                <div class="avatar-ph">{{ strtoupper(substr($permission->user->name ?? 'N', 0, 1)) }}</div>
                                            @endif
                                            <div>
                                                <p style="font-weight:600; font-size:13px; line-height:1.3;">{{ $permission->user->name ?? 'N/A' }}</p>
                                                <p style="font-size:11px; color:var(--muted);">{{ $permission->department->name ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Tipo --}}
                                    <td class="hide-mobile">
                                        @if($permission->entry_exit_type === 'entrada')
                                            <span class="tipo-tag tipo-entrada">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                                                Entrada
                                            </span>
                                        @else
                                            <span class="tipo-tag tipo-salida">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                                Salida
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Fecha --}}
                                    <td class="hide-mobile" style="font-size:13px; color:var(--muted); white-space:nowrap;">
                                        {{ \Carbon\Carbon::parse($permission->date)->format('d/m/Y') }}
                                    </td>

                                    {{-- Motivo truncado --}}
                                    <td class="motivo-cell">
                                        <p class="motivo-text" title="{{ $permission->reason }}">{{ $permission->reason }}</p>
                                    </td>

                                    {{-- Estado --}}
                                    <td>
                                        @php $s = $permission->status; @endphp
                                        <span class="badge badge-{{ $s }}">
                                            <span class="badge-dot"></span>
                                            {{ ucfirst($s) }}
                                        </span>
                                    </td>

                                    {{-- Acciones --}}
                                    <td style="text-align:right;">
                                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px; flex-wrap:wrap;">
                                            <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-gold btn-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Ver
                                            </a>
                                            @if(auth()->user()->hasRole('jefe') && $permission->status === 'pendiente')
                                                <form action="{{ route('permissions.approve', $permission->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-green btn-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Aprobar
                                                    </button>
                                                </form>
                                                <form action="{{ route('permissions.reject', $permission->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-rose btn-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        Rechazar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p style="font-size:15px; font-weight:600; color:var(--text); margin-bottom:4px;">Sin permisos</p>
                                        <p style="font-size:13px;">No se encontraron permisos con los filtros aplicados.</p>
                                        <a href="{{ route('permissions.index') }}" class="btn btn-outline" style="margin-top:16px;">Limpiar filtros</a>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="padding:16px 24px; border-top:1px solid var(--border);">
                    {{ $permissions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>