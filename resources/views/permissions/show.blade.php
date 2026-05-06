<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('permissions.index') }}" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Detalle del Permiso</h2>
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

        /* Banner de estado */
        .status-banner {
            padding:24px 32px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;
            position:relative; overflow:hidden;
        }
        .banner-pendiente { background:linear-gradient(135deg, #92400e 0%, #d97706 100%); }
        .banner-aprobado  { background:linear-gradient(135deg, #14532d 0%, #16a34a 100%); }
        .banner-rechazado { background:linear-gradient(135deg, var(--brand-dark) 0%, var(--rose) 100%); }
        .status-banner::before { content:''; position:absolute; top:-40px; right:-40px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.07); }

        .banner-emp { display:flex; align-items:center; gap:14px; z-index:1; }
        .banner-avatar { width:52px; height:52px; border-radius:50%; border:3px solid rgba(255,255,255,.3); object-fit:cover; flex-shrink:0; }
        .banner-ph { width:52px; height:52px; border-radius:50%; border:3px solid rgba(255,255,255,.3); background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:800; color:#fff; flex-shrink:0; }
        .banner-name { font-size:18px; font-weight:700; color:#fff; }
        .banner-sub  { font-size:13px; color:rgba(255,255,255,.75); margin-top:2px; }

        .status-chip { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); border-radius:20px; padding:5px 14px; font-size:13px; font-weight:700; color:#fff; z-index:1; }
        .status-chip .dot { width:8px; height:8px; border-radius:50%; background:#fff; opacity:.8; }

        /* Secciones */
        .detail-section { padding:24px 32px; border-bottom:1px solid #f0e6f5; }
        .detail-section:last-child { border-bottom:none; }
        .section-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); margin-bottom:16px; display:flex; align-items:center; gap:8px; }
        .section-title::after { content:''; flex:1; height:1px; background:linear-gradient(to right,#e0c8e8,transparent); }

        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        @media(max-width:540px){ .info-grid{grid-template-columns:1fr;} }

        .info-item { display:flex; flex-direction:column; gap:3px; }
        .info-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--muted); }
        .info-value { font-size:14px; font-weight:600; color:var(--text); }

        /* Tipo entrada/salida */
        .tipo-tag { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:8px; font-size:13px; font-weight:700; }
        .tipo-entrada { background:#e0eaff; color:#1d4ed8; }
        .tipo-salida  { background:var(--gold-pale); color:var(--brown); }

        /* Motivo en caja */
        .reason-box { background:var(--beige-light); border:1px solid var(--border); border-radius:10px; padding:16px; font-size:14px; color:var(--text); line-height:1.6; white-space:pre-wrap; word-break:break-word; }

        /* Documento */
        .doc-preview img  { max-width:100%; border-radius:10px; border:1px solid var(--border); }
        .doc-preview iframe { width:100%; height:340px; border-radius:10px; border:1px solid var(--border); }
        .no-doc { display:flex; align-items:center; gap:8px; padding:12px 16px; background:var(--beige-light); border:1px dashed var(--border); border-radius:9px; font-size:13px; color:var(--muted); }

        /* Acciones jefe */
        .actions-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 20px; height:40px; border-radius:9px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-outline { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-outline:hover { border-color:var(--brand-mid); color:var(--brand); }
        .btn-green  { background:#2d7a4f; color:#fff; }
        .btn-green:hover  { background:#236040; transform:translateY(-1px); color:#fff; }
        .btn-rose   { background:var(--rose); color:#fff; }
        .btn-rose:hover   { background:#923d59; transform:translateY(-1px); color:#fff; }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="detail-card">

                @php $s = $permission->status; @endphp

                {{-- Banner --}}
                <div class="status-banner banner-{{ $s }}">
                    <div class="banner-emp">
                        @if($permission->user && $permission->user->foto_perfil)
                            <img src="{{ asset('storage/' . $permission->user->foto_perfil) }}" class="banner-avatar" alt="">
                        @else
                            <div class="banner-ph">{{ strtoupper(substr($permission->user->name ?? 'N', 0, 1)) }}</div>
                        @endif
                        <div>
                            <p class="banner-name">{{ $permission->user->name ?? 'Sin asignar' }}</p>
                            <p class="banner-sub">{{ $permission->department->name ?? '' }} · {{ \Carbon\Carbon::parse($permission->date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="status-chip">
                        <span class="dot"></span>
                        {{ ucfirst($s) }}
                    </div>
                </div>

                {{-- Información del permiso --}}
                <div class="detail-section">
                    <p class="section-title">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Datos del Permiso
                    </p>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Tipo de Permiso</span>
                            @if($permission->entry_exit_type === 'entrada')
                                <span class="tipo-tag tipo-entrada">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                                    Entrada tardía
                                </span>
                            @else
                                <span class="tipo-tag tipo-salida">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    Salida anticipada
                                </span>
                            @endif
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha del Permiso</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($permission->date)->format('d \d\e F \d\e Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Horario Oficial</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($permission->official_schedule)->format('h:i A') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Hora de Entrada/Salida</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($permission->entry_exit_time)->format('h:i A') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Motivo --}}
                <div class="detail-section">
                    <p class="section-title">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                        Motivo
                    </p>
                    <div class="reason-box">{{ $permission->reason }}</div>
                </div>

                {{-- Documento --}}
                <div class="detail-section">
                    <p class="section-title">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Documento de Soporte
                    </p>
                    @if($permission->supporting_document)
                        <div class="doc-preview">
                            @if(pathinfo($permission->supporting_document, PATHINFO_EXTENSION) === 'pdf')
                                <iframe src="{{ asset('storage/' . $permission->supporting_document) }}"></iframe>
                                <div style="margin-top:8px;">
                                    <a href="{{ asset('storage/' . $permission->supporting_document) }}" target="_blank" style="font-size:13px; color:var(--brand); text-decoration:underline; display:inline-flex; align-items:center; gap:5px;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Abrir PDF en nueva pestaña
                                    </a>
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $permission->supporting_document) }}" alt="Documento de soporte">
                            @endif
                        </div>
                    @else
                        <div class="no-doc">
                            <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            No se adjuntó documento de soporte.
                        </div>
                    @endif
                </div>

                {{-- Acciones --}}
                <div class="detail-section" style="background:var(--beige-light);">
                    <div class="actions-row">
                        <a href="{{ route('permissions.index') }}" class="btn btn-outline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Volver
                        </a>

                        @if(auth()->user()->hasRole('jefe') && $permission->status === 'pendiente')
                            <form action="{{ route('permissions.approve', $permission->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-green">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Aprobar Permiso
                                </button>
                            </form>
                            <form action="{{ route('permissions.reject', $permission->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-rose">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Rechazar Permiso
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>