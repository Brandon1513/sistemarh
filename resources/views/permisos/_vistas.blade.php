{{--
    Botones de vista rápida para listas de permisos de ausencia.
    Parámetros: $ruta (nombre de la ruta), $vistas, $vista, $pendientes, $semana ([inicio, fin])
--}}
@php
    // Conserva búsqueda/estado/tipo al cambiar de vista; el rango de fechas lo reemplaza la vista
    $baseQuery = request()->except(['vista', 'page', 'start_date', 'end_date']);
    $semanaTexto = $semana[0]->locale('es')->translatedFormat('D j M') . ' – ' . $semana[1]->locale('es')->translatedFormat('D j M');
@endphp

<style>
    .vistas-bar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:12px 24px; background:#fff; border-bottom:1px solid var(--border); }
    .vistas-tabs { display:flex; flex-wrap:wrap; gap:6px; }
    .vista-tab { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:999px; border:1.5px solid var(--border); background:#fff; color:var(--muted); font-size:13px; font-weight:600; text-decoration:none; transition:all .15s; }
    .vista-tab:hover { border-color:var(--brand-mid); color:var(--brand); }
    .vista-tab.activa { background:var(--brand); border-color:var(--brand); color:#fff; }
    .vista-badge { min-width:20px; padding:1px 7px; border-radius:999px; background:#fdf3d7; color:#8a6100; font-size:11px; font-weight:700; text-align:center; }
    .vista-tab.activa .vista-badge { background:#fff; color:var(--brand); }
    .vistas-info { font-size:12px; color:var(--muted); }
    .vistas-info strong { color:var(--brand); }
</style>

<div class="vistas-bar">
    <div class="vistas-tabs">
        @foreach ($vistas as $clave => $etiqueta)
            <a href="{{ route($ruta, array_merge($baseQuery, ['vista' => $clave])) }}"
               class="vista-tab {{ $vista === $clave ? 'activa' : '' }}">
                {{ $etiqueta }}
                @if (str_contains($clave, 'pendientes') && $pendientes > 0)
                    <span class="vista-badge">{{ $pendientes }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="vistas-info">
        @if ($vista === 'rango')
            Filtrando por el rango de fechas seleccionado
        @elseif (str_starts_with($vista, 'semana'))
            Semana nominal: <strong>{{ $semanaTexto }}</strong>
            @if ($vista === 'semana_pendientes') · más todas las pendientes @endif
        @elseif ($vista === 'pendientes')
            Solicitudes pendientes de cualquier fecha, de la más antigua a la más reciente
        @else
            Todas las solicitudes, de la más reciente a la más antigua
        @endif
    </div>
</div>
