<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Días Inhábiles
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ $dias->count() }} registrados
            </span>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0;
            --gold:#D6A644; --gold-light:#EED39B; --gold-pale:#faf3e0; --brown:#473524; --rose:#AA4969;
            --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682;
        }
        .card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); margin-bottom:20px; }
        .card-head { padding:16px 24px; background:var(--beige-light); border-bottom:1.5px solid var(--border); display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; justify-content:space-between; }
        .filter-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); display:block; margin-bottom:5px; }
        .filter-select { height:38px; padding:0 12px; border:1.5px solid var(--border); border-radius:8px; font-size:13.5px; color:var(--text); background:#fff; min-width:200px; }
        .btn { display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); color:#fff; }
        .btn-outline { background:#fff; color:var(--muted); border:1.5px solid var(--border); }
        .btn-gold { background:var(--gold); color:#fff; }
        .btn-rose { background:var(--rose); color:#fff; }
        .btn-sm { height:30px; padding:0 11px; font-size:12px; border-radius:6px; }
        .tabla { width:100%; border-collapse:collapse; }
        .tabla th { padding:12px 16px; background:var(--beige-light); border-bottom:1.5px solid var(--border); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); text-align:left; }
        .tabla td { padding:13px 16px; font-size:14px; color:var(--text); border-bottom:1px solid #f0e6f5; vertical-align:middle; }
        .tabla tr:last-child td { border-bottom:none; }
        .tag { display:inline-block; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
        .tag-sede { background:var(--brand-light); color:var(--brand); }
        .tag-todas { background:var(--gold-pale); color:var(--brown); border:1px solid var(--gold-light); }
        .tag-cada { background:#e8f5ee; color:#1b6b38; }
        .tag-unico { background:#f0ecf3; color:var(--muted); }
        .pasado { color:var(--muted); font-size:12px; font-style:italic; }
        .alert-success { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }
        .nota { padding:14px 24px; font-size:13px; color:var(--muted); background:#fff; border-bottom:1px solid var(--border); }
        .ley-grid { padding:18px 24px; display:flex; flex-wrap:wrap; gap:8px; }
        .ley-chip { padding:6px 12px; border-radius:8px; background:var(--beige-light); border:1px solid var(--border); font-size:12.5px; color:var(--text); }
        .ley-chip strong { color:var(--brand); }
        .empty { text-align:center; padding:48px 24px; color:var(--muted); font-size:14px; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert-success">✓ {{ session('success') }}</div>
            @endif

            {{-- Días administrados por RH --}}
            <div class="card">
                <form method="GET" action="{{ route('dias_inhabiles.index') }}" class="card-head">
                    <div style="display:flex; gap:8px; align-items:flex-end;">
                        <div>
                            <label class="filter-label" for="sede">Aplica a</label>
                            <select name="sede" id="sede" class="filter-select" onchange="this.form.submit()">
                                <option value="">Todas las sedes y generales</option>
                                <option value="todas" {{ request('sede') === 'todas' ? 'selected' : '' }}>Solo los generales</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}" {{ request('sede') == $sede->id ? 'selected' : '' }}>Solo {{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(request()->filled('sede'))
                            <a href="{{ route('dias_inhabiles.index') }}" class="btn btn-outline">Limpiar</a>
                        @endif
                    </div>
                    <a href="{{ route('dias_inhabiles.create') }}" class="btn btn-primary">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Agregar día inhábil
                    </a>
                </form>

                <p class="nota">
                    Estos días se suman a los festivos de ley. Se aplican de inmediato a las nuevas solicitudes de
                    vacaciones, permisos y pases; las solicitudes que ya existen no se recalculan.
                </p>

                <div class="overflow-x-auto">
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Aplica a</th>
                                <th>Se repite</th>
                                <th>Próxima vez</th>
                                <th style="text-align:right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dias as $dia)
                                @php $proxima = $dia->proximaFecha(); @endphp
                                <tr>
                                    <td style="font-weight:700; color:var(--brand);">
                                        {{ \Carbon\Carbon::create(2000, $dia->mes, $dia->dia)->locale('es')->translatedFormat('j \d\e F') }}
                                        @if($dia->anio) <span style="font-weight:400; color:var(--muted);">{{ $dia->anio }}</span> @endif
                                    </td>
                                    <td>{{ $dia->nombre }}</td>
                                    <td>
                                        @if($dia->sede)
                                            <span class="tag tag-sede">{{ $dia->sede->nombre }}</span>
                                        @else
                                            <span class="tag tag-todas">Todas las sedes</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="tag {{ $dia->anio ? 'tag-unico' : 'tag-cada' }}">{{ $dia->anio ? 'Solo ese año' : 'Cada año' }}</span>
                                    </td>
                                    <td>
                                        @if($proxima && $proxima->gte(today()))
                                            {{ $proxima->locale('es')->translatedFormat('D j M Y') }}
                                        @else
                                            <span class="pasado">Ya pasó</span>
                                        @endif
                                    </td>
                                    <td style="text-align:right;">
                                        <div style="display:flex; justify-content:flex-end; gap:6px;">
                                            <a href="{{ route('dias_inhabiles.edit', $dia) }}" class="btn btn-gold btn-sm">Editar</a>
                                            <form action="{{ route('dias_inhabiles.destroy', $dia) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar «{{ addslashes($dia->nombre) }}»? Las nuevas solicitudes volverán a contar ese día como laboral.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-rose btn-sm">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6"><div class="empty">No hay días inhábiles con ese filtro.</div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Festivos de ley, solo consulta --}}
            <div class="card">
                <div class="card-head">
                    <div>
                        <span class="filter-label">Festivos de ley {{ now()->year }}</span>
                        <span style="font-size:13px; color:var(--muted);">Se aplican solos a todas las sedes; no hace falta capturarlos.</span>
                    </div>
                </div>
                <div class="ley-grid">
                    @foreach($festivosDeLey as $fecha => $nombre)
                        <span class="ley-chip">
                            <strong>{{ \Carbon\Carbon::parse($fecha)->locale('es')->translatedFormat('j M') }}</strong> · {{ $nombre }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>