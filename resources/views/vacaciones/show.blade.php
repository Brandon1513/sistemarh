<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('vacaciones.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detalle de Solicitud de Vacaciones</h2>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:       #6A2C75;
            --brand-dark:  #541f5c;
            --brand-light: #f3eef5;
            --brand-mid:   #BBA4C0;
            --gold:        #D6A644;
            --gold-pale:   #faf3e0;
            --gold-light:  #EED39B;
            --brown:       #473524;
            --rose:        #AA4969;
            --beige-light: #f7f3ef;
            --border:      #e6d9ed;
            --text:        #2c1a30;
            --muted:       #7a6682;
        }

        .detail-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }

        /* Banner */
        .status-banner { padding:24px 32px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; position:relative; overflow:hidden; }
        .banner-pendiente { background:linear-gradient(135deg,#92400e,#d97706); }
        .banner-aprobado  { background:linear-gradient(135deg,#14532d,#16a34a); }
        .banner-rechazado { background:linear-gradient(135deg,var(--brand-dark),var(--rose)); }
        .status-banner::before { content:''; position:absolute; top:-40px; right:-40px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.07); }
        .status-banner::after  { content:''; position:absolute; bottom:-50px; right:100px; width:140px; height:140px; border-radius:50%; background:rgba(255,255,255,.04); }

        .banner-emp   { display:flex; align-items:center; gap:14px; z-index:1; }
        .banner-avatar { width:54px; height:54px; border-radius:50%; border:3px solid rgba(255,255,255,.3); object-fit:cover; }
        .banner-ph    { width:54px; height:54px; border-radius:50%; border:3px solid rgba(255,255,255,.3); background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:800; color:#fff; }
        .banner-name  { font-size:18px; font-weight:700; color:#fff; }
        .banner-sub   { font-size:13px; color:rgba(255,255,255,.75); margin-top:2px; }
        .status-chip  { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); border-radius:20px; padding:5px 14px; font-size:13px; font-weight:700; color:#fff; z-index:1; }
        .status-chip .dot { width:8px; height:8px; border-radius:50%; background:#fff; opacity:.8; }

        /* Secciones */
        .detail-section { padding:24px 32px; border-bottom:1px solid #f0e6f5; }
        .detail-section:last-child { border-bottom:none; }
        .section-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .section-title::after { content:''; flex:1; height:1px; background:linear-gradient(to right,#e0c8e8,transparent); }

        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        @media(max-width:540px){ .info-grid{grid-column:1fr;} }
        .info-item  { display:flex; flex-direction:column; gap:3px; }
        .info-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
        .info-value { font-size:14px; font-weight:600; color:var(--text); }

        /* Período visual */
        .period-visual { display:flex; align-items:center; gap:12px; padding:16px; background:var(--brand-light); border:1px solid var(--border); border-radius:10px; flex-wrap:wrap; }
        .pv-date { text-align:center; min-width:60px; }
        .pv-day  { font-size:26px; font-weight:800; color:var(--brand); line-height:1; }
        .pv-mon  { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; }
        .pv-arrow { font-size:20px; color:var(--brand-mid); flex:1; text-align:center; }
        .pv-days  { margin-left:auto; display:flex; flex-direction:column; align-items:center; gap:4px; }
        .pv-count { font-size:28px; font-weight:800; color:var(--brand); line-height:1; }
        .pv-label { font-size:11px; color:var(--muted); }

        /* Reincorporación */
        .reinc-box { display:flex; align-items:center; gap:10px; padding:12px 14px; background:var(--gold-pale); border:1px solid var(--gold-light); border-radius:9px; margin-top:12px; }
        .reinc-box svg { color:var(--brown); flex-shrink:0; }
        .reinc-box p { margin:0; font-size:13px; color:var(--brown); font-weight:600; }

        /* Stats días */
        .days-stats { display:flex; gap:12px; flex-wrap:wrap; }
        .day-stat { flex:1; min-width:80px; padding:14px; background:var(--beige-light); border:1px solid var(--border); border-radius:10px; text-align:center; }
        .day-stat-val { font-size:22px; font-weight:800; color:var(--brand); line-height:1; }
        .day-stat-lbl { font-size:11px; color:var(--muted); margin-top:4px; }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 20px; height:40px; border-radius:9px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-outline { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-outline:hover { border-color:var(--brand-mid); color:var(--brand); }
        .btn-green { background:#2d7a4f; color:#fff; }
        .btn-green:hover { background:#236040; transform:translateY(-1px); color:#fff; }
        .btn-rose  { background:var(--rose); color:#fff; }
        .btn-rose:hover { background:#923d59; transform:translateY(-1px); color:#fff; }
        .btn-danger { background:#dc2626; color:#fff; }
        .btn-danger:hover { background:#b91c1c; transform:translateY(-1px); color:#fff; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="detail-card">

                @php
                    $estado  = $vacationRequest->estado;
                    $inicio  = \Carbon\Carbon::parse($vacationRequest->fecha_inicio);
                    $fin     = \Carbon\Carbon::parse($vacationRequest->fecha_fin);
                    $reinc   = $vacationRequest->fecha_reincorporacion ? \Carbon\Carbon::parse($vacationRequest->fecha_reincorporacion) : null;
                    $dias    = $vacationRequest->dias_solicitados ?? ($inicio->diffInDays($fin) + 1);
                @endphp

                {{-- Banner --}}
                <div class="status-banner banner-{{ $estado }}">
                    <div class="banner-emp">
                        @if($vacationRequest->empleado && $vacationRequest->empleado->foto_perfil)
                            <img src="{{ asset('storage/' . $vacationRequest->empleado->foto_perfil) }}" class="banner-avatar" alt="">
                        @else
                            <div class="banner-ph">{{ strtoupper(substr($vacationRequest->empleado->name ?? 'N', 0, 1)) }}</div>
                        @endif
                        <div>
                            <p class="banner-name">{{ $vacationRequest->empleado->name ?? 'N/A' }}</p>
                            <p class="banner-sub">
                                {{ $vacationRequest->departamento->name ?? '' }} · Período {{ $vacationRequest->periodo_correspondiente }}
                            </p>
                        </div>
                    </div>
                    <div class="status-chip">
                        <span class="dot"></span>
                        {{ ucfirst($estado) }}
                    </div>
                </div>

                {{-- Período visual --}}
                <div class="detail-section">
                    <p class="section-title">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Período Vacacional
                    </p>
                    <div class="period-visual">
                        <div class="pv-date">
                            <div class="pv-day">{{ $inicio->format('d') }}</div>
                            <div class="pv-mon">{{ $inicio->translatedFormat('M Y') }}</div>
                        </div>
                        <div class="pv-arrow">→</div>
                        <div class="pv-date">
                            <div class="pv-day">{{ $fin->format('d') }}</div>
                            <div class="pv-mon">{{ $fin->translatedFormat('M Y') }}</div>
                        </div>
                        <div class="pv-days">
                            <div class="pv-count">{{ $dias }}</div>
                            <div class="pv-label">días</div>
                        </div>
                    </div>

                    @if($reinc)
                        <div class="reinc-box">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            <p>Reincorporación: <strong>{{ $reinc->translatedFormat('d \d\e F \d\e Y') }}</strong></p>
                        </div>
                    @endif
                </div>

                {{-- Estadísticas de días --}}
                <div class="detail-section">
                    <p class="section-title">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Resumen de Días
                    </p>
                    <div class="days-stats">
                        <div class="day-stat">
                            <div class="day-stat-val">{{ $vacationRequest->dias_solicitados ?? $dias }}</div>
                            <div class="day-stat-lbl">Solicitados</div>
                        </div>
                        @if($vacationRequest->pendientes_disfrutar !== null)
                            <div class="day-stat">
                                <div class="day-stat-val" style="color:var(--gold);">{{ $vacationRequest->pendientes_disfrutar }}</div>
                                <div class="day-stat-lbl">Pendientes por disfrutar</div>
                            </div>
                        @endif
                        <div class="day-stat">
                            <div class="day-stat-val" style="color:var(--muted);">{{ $vacationRequest->periodo_correspondiente }}</div>
                            <div class="day-stat-lbl">Período</div>
                        </div>
                        <div class="day-stat">
                            <div class="day-stat-val" style="color:var(--muted); font-size:14px;">
                                {{ $vacationRequest->fecha_solicitud ? \Carbon\Carbon::parse($vacationRequest->fecha_solicitud)->format('d/m/Y') : $vacationRequest->created_at->format('d/m/Y') }}
                            </div>
                            <div class="day-stat-lbl">Fecha de solicitud</div>
                        </div>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="detail-section" style="background:var(--beige-light);">
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <a href="{{ route('vacaciones.index') }}" class="btn btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Volver
                        </a>

                        @if($vacationRequest->estado === 'pendiente')
                            @if($vacationRequest->empleado_id === auth()->id())
                                <form action="{{ route('vacaciones.destroy', $vacationRequest->id) }}" method="POST" style="display:inline;"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta solicitud?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Eliminar solicitud
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('vacaciones.aprobar', $vacationRequest->id) }}" class="btn btn-green">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Aprobar
                                </a>
                                <a href="{{ route('vacaciones.rechazar', $vacationRequest->id) }}" class="btn btn-rose">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Rechazar
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>