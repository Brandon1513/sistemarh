<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Usuarios Registrados
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ $users->total() }} empleados
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
            --beige:       #D7C9B9;
            --beige-light: #f7f3ef;
            --surface:     #ffffff;
            --border:      #e6d9ed;
            --text:        #2c1a30;
            --muted:       #7a6682;
        }

        .emp-card {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(106,44,117,.08), 0 8px 28px rgba(106,44,117,.07);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .filter-bar {
            background: var(--beige-light);
            border-bottom: 1.5px solid var(--border);
            padding: 20px 24px;
            display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 180px; }
        .filter-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--brand); }

        .filter-input, .filter-select {
            height: 38px; padding: 0 12px;
            border: 1.5px solid var(--border); border-radius: 8px;
            font-size: 13.5px; color: var(--text); background: #fff;
            transition: border-color .2s, box-shadow .2s; outline: none; width: 100%;
        }
        .filter-input:focus, .filter-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(106,44,117,.12);
        }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 0 16px; height: 38px; border-radius: 8px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            border: none; transition: all .18s; white-space: nowrap; text-decoration: none;
        }
        .btn-primary  { background: var(--brand); color: #fff; }
        .btn-primary:hover { background: var(--brand-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(106,44,117,.3); color: #fff; }
        .btn-outline  { background: #fff; color: var(--muted); border: 1.5px solid var(--border); }
        .btn-outline:hover { border-color: var(--brand-mid); color: var(--brand); }
        .btn-gold     { background: var(--gold); color: #fff; }
        .btn-gold:hover { background: #bf922d; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(214,166,68,.35); color:#fff; }
        .btn-rose     { background: var(--rose); color: #fff; }
        .btn-rose:hover { background: #923d59; color:#fff; }
        .btn-success  { background: #2d7a4f; color: #fff; }
        .btn-success:hover { background: #236040; color:#fff; }
        .btn-brown    { background: var(--brown); color: #fff; }
        .btn-sm { height: 32px; padding: 0 12px; font-size: 12px; border-radius: 6px; }
        .btn-disabled { opacity: .4; cursor: not-allowed !important; pointer-events: none; }

        .emp-table { width: 100%; border-collapse: collapse; }
        .emp-table thead th {
            padding: 12px 16px;
            background: var(--beige-light);
            border-bottom: 1.5px solid var(--border);
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
            color: var(--brand); text-align: left;
        }
        .emp-table tbody tr { border-bottom: 1px solid #f0e6f5; transition: background .15s; }
        .emp-table tbody tr:hover { background: #faf4fc; }
        .emp-table tbody tr:last-child { border-bottom: none; }
        .emp-table td { padding: 14px 16px; font-size: 14px; color: var(--text); vertical-align: middle; }

        .avatar { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
        .avatar-placeholder {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-mid) 100%);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 14px; font-weight: 700; flex-shrink: 0;
        }

        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-active   { background: #e6f4ec; color: #1b6b38; }
        .badge-inactive { background: #fce8ee; color: var(--rose); }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; }
        .badge-active   .badge-dot { background: #2d7a4f; }
        .badge-inactive .badge-dot { background: var(--rose); }

        .role-tag {
            display: inline-block;
            background: var(--gold-pale); color: var(--brown);
            border: 1px solid var(--gold-light);
            border-radius: 4px; font-size: 11px; font-weight: 600; padding: 2px 7px; margin-right: 3px;
        }
        .you-tag {
            font-size: 10px; font-weight: 700;
            background: var(--brand-light); color: var(--brand);
            border: 1px solid var(--brand-mid);
            border-radius: 4px; padding: 1px 6px; margin-left: 6px; vertical-align: middle;
        }

        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13.5px; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #e8f5ee; color: #1b6b38; border: 1px solid #b3dfc3; }
        .alert-warning { background: #fce8ee; color: #7a2039; border: 1px solid #f0b3c3; }

        .empty-state { text-align: center; padding: 64px 24px; color: var(--muted); }
        .empty-state svg { width: 56px; height: 56px; margin: 0 auto 16px; opacity: .3; }

        @media (max-width: 640px) { .hide-mobile { display: none; } .filter-bar { padding: 16px; } }
    </style>

    <div class="py-10" style="background-image: url('{{ asset('images/background-pattern.png') }}'); min-height: calc(100vh - 64px);">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="alert alert-success">
                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">
                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    {{ session('warning') }}
                </div>
            @endif

            <div class="emp-card">

                {{-- Filtros --}}
                <form method="GET" action="{{ route('empleados.index') }}">
                    <div class="filter-bar">
                        <div class="filter-group" style="flex:2; min-width:220px;">
                            <span class="filter-label">Buscar</span>
                            <div style="position:relative;">
                                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#BBA4C0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                                </svg>
                                <input type="text" name="search" placeholder="Nombre o correo..." value="{{ request('search') }}" class="filter-input" style="padding-left:34px;">
                            </div>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Estado</span>
                            <select name="estado" class="filter-select">
                                <option value="">Todos</option>
                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Departamento</span>
                            <select name="departamento" class="filter-select">
                                <option value="">Todos</option>
                                @foreach ($departamentos as $dep)
                                    <option value="{{ $dep->id }}" {{ request('departamento') == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Rol</span>
                            <select name="rol" class="filter-select">
                                <option value="">Todos</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('rol') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:flex; gap:8px; align-items:flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Filtrar
                            </button>
                            @if(request()->hasAny(['search','estado','departamento','rol']))
                                <a href="{{ route('empleados.index') }}" class="btn btn-outline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Sub-header --}}
                <div style="padding:14px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); background:#fff;">
                    <p style="font-size:13px; color:var(--muted);">
                        Mostrando <strong style="color:var(--text);">{{ $users->firstItem() }}–{{ $users->lastItem() }}</strong>
                        de <strong style="color:var(--text);">{{ $users->total() }}</strong> resultados
                    </p>
                    <a href="{{ route('empleados.create') }}" class="btn btn-primary btn-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar Usuario
                    </a>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="emp-table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th class="hide-mobile">Correo</th>
                                <th class="hide-mobile">Departamento</th>
                                <th class="hide-mobile">Rol</th>
                                <th>Estado</th>
                                <th style="text-align:right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php $isMe = auth()->id() === $user->id; @endphp
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            @if($user->foto_perfil)
                                                <img src="{{ asset('storage/' . $user->foto_perfil) }}" alt="{{ $user->name }}" class="avatar">
                                            @else
                                                <div class="avatar-placeholder">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                            @endif
                                            <div>
                                                <p style="font-weight:600; font-size:14px; line-height:1.3;">
                                                    {{ $user->name }}
                                                    @if($isMe)<span class="you-tag">Tú</span>@endif
                                                </p>
                                                <p style="font-size:12px; color:var(--muted);">{{ $user->puesto_empleado }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hide-mobile" style="color:var(--muted); font-size:13px;">{{ $user->email }}</td>
                                    <td class="hide-mobile" style="font-size:13px;">{{ $user->departamento->name ?? '—' }}</td>
                                    <td class="hide-mobile">
                                        @foreach($user->roles as $role)
                                            <span class="role-tag">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->activo ? 'badge-active' : 'badge-inactive' }}">
                                            <span class="badge-dot"></span>
                                            {{ $user->activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                                            <a href="{{ route('empleados.edit', $user->id) }}" class="btn btn-gold btn-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Editar
                                            </a>
                                            @if($isMe)
                                                <button class="btn btn-brown btn-sm btn-disabled" title="No puedes inactivar tu propia cuenta">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-6a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                                    Protegido
                                                </button>
                                            @else
                                                <form action="{{ route('empleados.toggle', $user->id) }}" method="POST" style="display:inline;"
                                                    onsubmit="return confirmToggle('{{ $user->activo ? 'inactivar' : 'activar' }}', '{{ addslashes($user->name) }}')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $user->activo ? 'btn-rose' : 'btn-success' }}">
                                                        @if($user->activo)
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                            Inactivar
                                                        @else
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Activar
                                                        @endif
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6">
                                    <div class="empty-state">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <p style="font-size:15px; font-weight:600; color:var(--text); margin-bottom:4px;">Sin resultados</p>
                                        <p style="font-size:13px;">No se encontraron empleados con los filtros aplicados.</p>
                                        <a href="{{ route('empleados.index') }}" class="btn btn-outline" style="margin-top:16px;">Limpiar filtros</a>
                                    </div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="padding:16px 24px; border-top:1px solid var(--border);">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmToggle(action, userName) {
            return confirm(`¿Estás seguro de que deseas ${action} al usuario ${userName}?`);
        }
    </script>
</x-app-layout>