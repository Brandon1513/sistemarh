<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <span style="font-size:20px;">🫂</span> Grupos
            </h2>
            <span class="px-3 py-1 text-sm font-medium rounded-full" style="background:#f3eef5; color:#6A2C75;">
                {{ $grupos->count() }} {{ $grupos->count() === 1 ? 'grupo' : 'grupos' }}
            </span>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0;
            --gold:#D6A644; --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef;
            --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682;
        }
        .card { background:#fff; border-radius:16px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; }
        .toolbar { padding:18px 24px; background:var(--beige-light); border-bottom:1.5px solid var(--border); display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
        .toolbar p { font-size:13px; color:var(--muted); }
        .btn { display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; white-space:nowrap; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); color:#fff; }
        .btn-sm { height:30px; padding:0 11px; font-size:12px; border-radius:6px; }
        .btn-gold { background:var(--gold); color:#fff; }
        .btn-rose { background:var(--rose); color:#fff; }
        .btn-gray { background:#f0ecf3; color:var(--muted); }
        .btn-green { background:#2d7a4f; color:#fff; }
        .fila { display:grid; grid-template-columns:48px minmax(0,1fr) auto; gap:16px; align-items:center; padding:16px 24px; border-bottom:1px solid #f0e6f5; }
        .fila:last-child { border-bottom:none; }
        .fila:hover { background:#faf4fc; }
        .fila.inactiva .icono, .fila.inactiva .nombre, .fila.inactiva .desc { opacity:.5; }
        .icono { width:48px; height:48px; border-radius:12px; background:var(--brand-light); display:flex; align-items:center; justify-content:center; font-size:22px; }
        .nombre { font-size:15px; font-weight:700; color:var(--text); }
        .desc { font-size:13px; color:var(--muted); margin-top:2px; }
        .meta { display:flex; flex-wrap:wrap; gap:6px 14px; margin-top:6px; font-size:12px; color:var(--muted); }
        .chip { display:inline-flex; padding:2px 9px; border-radius:20px; font-size:11.5px; font-weight:700; background:var(--brand-light); color:var(--brand); }
        .chip-uso { background:#e0eaff; color:#2b4c9b; }
        .chip-inactivo { background:#f0ecf3; color:var(--muted); }
        .acciones { display:flex; gap:6px; }
        @media(max-width:640px){ .fila { grid-template-columns:40px minmax(0,1fr); } .acciones { grid-column:1 / -1; } }
        .alert { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; }
        .alert-success { background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }
        .alert-warning { background:#faf3e0; color:#7a5a12; border:1px solid #EED39B; }
        .vacio { text-align:center; padding:56px 24px; color:var(--muted); }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">⚠ {{ session('warning') }}</div>
            @endif

            <div class="card">
                <div class="toolbar">
                    <p>Los grupos sirven para dirigir comunicados a un conjunto de personas, de cualquier sede o área.</p>
                    <a href="{{ route('grupos.create') }}" class="btn btn-primary">＋ Nuevo grupo</a>
                </div>

                @forelse($grupos as $grupo)
                    @php $enUso = $usos[$grupo->id] ?? 0; @endphp
                    <div class="fila {{ $grupo->activo ? '' : 'inactiva' }}">
                        <div class="icono">🫂</div>
                        <div style="min-width:0;">
                            <div class="nombre">{{ $grupo->nombre }}</div>
                            @if($grupo->descripcion)<div class="desc">{{ $grupo->descripcion }}</div>@endif
                            <div class="meta">
                                @unless($grupo->activo)<span class="chip chip-inactivo">Inactivo</span>@endunless
                                <span class="chip">{{ $grupo->usuarios_count }} {{ $grupo->usuarios_count === 1 ? 'integrante' : 'integrantes' }}</span>
                                @if($enUso)<span class="chip chip-uso">En {{ $enUso }} {{ $enUso === 1 ? 'comunicado' : 'comunicados' }}</span>@endif
                                @if($grupo->creador)<span>Creado por {{ $grupo->creador->name }}</span>@endif
                            </div>
                        </div>
                        <div class="acciones">
                            <a href="{{ route('grupos.edit', $grupo) }}" class="btn btn-gold btn-sm">Editar</a>

                            @if($enUso)
                                {{-- En uso: no se puede borrar, solo inactivar --}}
                                <form method="POST" action="{{ route('grupos.toggle', $grupo) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm {{ $grupo->activo ? 'btn-gray' : 'btn-green' }}"
                                            title="{{ $grupo->activo ? 'Ya no aparecerá para comunicados nuevos' : 'Vuelve a aparecer para comunicados nuevos' }}">
                                        {{ $grupo->activo ? 'Inactivar' : 'Activar' }}
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('grupos.destroy', $grupo) }}"
                                      onsubmit="return confirm('¿Eliminar el grupo «{{ addslashes($grupo->nombre) }}»?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-rose btn-sm">Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="vacio">
                        <div style="font-size:40px; margin-bottom:8px;">🫂</div>
                        <p style="font-weight:700; color:var(--text);">Todavía no hay grupos</p>
                        <p style="font-size:13px;">Crea el primero, por ejemplo "Comité de seguridad" o "Jefes de planta".</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>