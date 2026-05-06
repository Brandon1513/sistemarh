<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('permisos.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detalle del Permiso de Ausencia</h2>
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

        .banner-emp   { display:flex; align-items:center; gap:14px; z-index:1; }
        .banner-avatar { width:52px; height:52px; border-radius:50%; border:3px solid rgba(255,255,255,.3); object-fit:cover; }
        .banner-ph    { width:52px; height:52px; border-radius:50%; border:3px solid rgba(255,255,255,.3); background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:800; color:#fff; }
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
        @media(max-width:540px){ .info-grid{grid-template-columns:1fr;} }
        .info-item  { display:flex; flex-direction:column; gap:3px; }
        .info-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
        .info-value { font-size:14px; font-weight:600; color:var(--text); }

        /* Tags */
        .tipo-tag { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:8px; font-size:13px; font-weight:700; }
        .tipo-permiso    { background:var(--brand-light); color:var(--brand); }
        .tipo-comision   { background:var(--gold-pale); color:var(--brown); }
        .tipo-suspension { background:#fce8ee; color:var(--rose); }
        .goce-con { background:#e8f5ee; color:#1b6b38; }
        .goce-sin { background:#fff3cd; color:#856404; }

        /* Período visual */
        .period-box { display:flex; align-items:center; gap:12px; padding:14px 16px; background:var(--brand-light); border:1px solid var(--border); border-radius:10px; }
        .period-date { text-align:center; }
        .period-day  { font-size:22px; font-weight:800; color:var(--brand); line-height:1; }
        .period-mon  { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; }
        .period-arrow { color:var(--brand-mid); font-size:18px; flex:1; text-align:center; }
        .period-days { font-size:12px; font-weight:700; background:var(--brand); color:#fff; border-radius:20px; padding:2px 10px; }

        /* Motivo */
        .reason-box { background:var(--beige-light); border:1px solid var(--border); border-radius:10px; padding:16px; font-size:14px; color:var(--text); line-height:1.6; white-space:pre-wrap; word-break:break-word; }

        /* Documento */
        .doc-preview img   { max-width:100%; border-radius:10px; border:1px solid var(--border); }
        .doc-preview iframe { width:100%; height:340px; border-radius:10px; border:1px solid var(--border); }
        .no-doc { display:flex; align-items:center; gap:8px; padding:12px 16px; background:var(--beige-light); border:1px dashed var(--border); border-radius:9px; font-size:13px; color:var(--muted); }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 20px; height:40px; border-radius:9px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-outline { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-outline:hover { border-color:var(--brand-mid); color:var(--brand); }
        .btn-purple { background:var(--brand); color:#fff; }
        .btn-purple:hover { background:var(--brand-dark); transform:translateY(-1px); color:#fff; }
        .btn-green { background:#2d7a4f; color:#fff; }
        .btn-green:hover { background:#236040; transform:translateY(-1px); color:#fff; }
        .btn-rose  { background:var(--rose); color:#fff; }
        .btn-rose:hover { background:#923d59; transform:translateY(-1px); color:#fff; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="detail-card">

                @php
                    $estado = $solicitud->estado;
                    $inicio = \Carbon\Carbon::parse($solicitud->fecha_inicio);
                    $fin    = \Carbon\Carbon::parse($solicitud->fecha_termino);
                    $dias   = $inicio->diffInDays($fin) + 1;
                @endphp

                {{-- Banner --}}
                <div class="status-banner banner-{{ $estado }}">
                    <div class="banner-emp">
                        @if($solicitud->empleado && $solicitud->empleado->foto_perfil)
                            <img src="{{ asset('storage/' . $solicitud->empleado->foto_perfil) }}" class="banner-avatar" alt="">
                        @else
                            <div class="banner-ph">{{ strtoupper(substr($solicitud->empleado->name ?? 'N', 0, 1)) }}</div>
                        @endif
                        <div>
                            <p class="banner-name">{{ $solicitud->empleado->name ?? 'N/A' }}</p>
                            <p class="banner-sub">{{ $solicitud->departamento->name ?? '' }} · Creado el {{ $solicitud->created_at->format('d/m/Y') }}</p>
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
                        Período
                    </p>
                    <div class="period-box">
                        <div class="period-date">
                            <div class="period-day">{{ $inicio->format('d') }}</div>
                            <div class="period-mon">{{ $inicio->translatedFormat('M Y') }}</div>
                        </div>
                        <div class="period-arrow">→</div>
                        <div class="period-date">
                            <div class="period-day">{{ $fin->format('d') }}</div>
                            <div class="period-mon">{{ $fin->translatedFormat('M Y') }}</div>
                        </div>
                        <div style="margin-left:auto;">
                            <span class="period-days">{{ $dias }} día{{ $dias !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    @if($solicitud->fecha_regreso_laborar)
                        <p style="font-size:13px; color:var(--muted); margin-top:10px;">
                            <svg class="w-3.5 h-3.5" style="display:inline;vertical-align:middle;margin-right:3px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Regreso a laborar: <strong>{{ \Carbon\Carbon::parse($solicitud->fecha_regreso_laborar)->format('d/m/Y') }}</strong>
                        </p>
                    @endif
                </div>

                {{-- Clasificación --}}
                <div class="detail-section">
                    <p class="section-title">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Clasificación
                    </p>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Tipo de Solicitud</span>
                            @php
                                $tipoClass = match($solicitud->tipo) {
                                    'Comisión'   => 'tipo-comision',
                                    'Suspensión' => 'tipo-suspension',
                                    default      => 'tipo-permiso',
                                };
                            @endphp
                            <span class="tipo-tag {{ $tipoClass }}">{{ $solicitud->tipo }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tipo de Permiso</span>
                            <span class="tipo-tag {{ $solicitud->tipo_permiso === 'Con Goce de Sueldo' ? 'goce-con' : 'goce-sin' }}">
                                {{ $solicitud->tipo_permiso }}
                            </span>
                        </div>
                        @if($solicitud->dia_descanso)
                            <div class="info-item">
                                <span class="info-label">Día de Descanso</span>
                                <span class="info-value">{{ $solicitud->dia_descanso }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Motivo --}}
                <div class="detail-section">
                    <p class="section-title">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        Observaciones / Motivo
                    </p>
                    <div class="reason-box">{{ $solicitud->motivo }}</div>
                </div>

                {{-- Archivo --}}
                <div class="detail-section">
                    <p class="section-title">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Archivo Adjunto
                    </p>
                    @if($solicitud->archivo)
                        @php
                            $ext = pathinfo($solicitud->archivo, PATHINFO_EXTENSION);
                            $url = \Illuminate\Support\Facades\Storage::url($solicitud->archivo);
                        @endphp
                        <div class="doc-preview">
                            @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ $url }}" alt="Archivo adjunto">
                            @elseif($ext === 'pdf')
                                <iframe src="{{ $url }}"></iframe>
                            @endif
                            <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
                                <a href="{{ $url }}" download class="btn btn-purple" style="height:36px; font-size:13px;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Descargar archivo
                                </a>
                                @if($ext === 'pdf')
                                    <a href="{{ $url }}" target="_blank" class="btn btn-outline" style="height:36px; font-size:13px;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Abrir PDF en nueva pestaña
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="no-doc">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            No se adjuntó archivo.
                        </div>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="detail-section" style="background:var(--beige-light);">
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <a href="{{ route('permisos.index') }}" class="btn btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Volver
                        </a>
                        @if(auth()->user()->hasRole('jefe') && $solicitud->estado === 'pendiente' && $solicitud->empleado_id !== auth()->id())
                            <form action="{{ route('permisos.aprobar', $solicitud->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-green">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Aprobar
                                </button>
                            </form>
                            <form action="{{ route('permisos.rechazar', $solicitud->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-rose">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Rechazar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>