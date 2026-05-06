<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                RH — Pases de Entrada/Salida
            </h2>
            @if(!request()->hasAny(['search','start_date','end_date']))
                <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background:#f3eef5; color:#6A2C75;">
                    📅 Semana nominal actual
                </span>
            @endif
        </div>
    </x-slot>

    @include('rh._styles')

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="rh-alert rh-alert-success">
                    <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="rh-card">

                {{-- Filtros --}}
                <form method="GET" action="{{ route('rh.index') }}">
                    <div class="rh-filter-bar">
                        <div class="rh-filter-group" style="flex:2; min-width:200px;">
                            <span class="rh-filter-label">Buscar empleado</span>
                            <div style="position:relative;">
                                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#BBA4C0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
                                <input type="text" name="search" placeholder="Nombre del empleado..." value="{{ request('search') }}" class="rh-filter-input" style="padding-left:34px;">
                            </div>
                        </div>
                        <div class="rh-filter-group">
                            <span class="rh-filter-label">Desde</span>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="rh-filter-input">
                        </div>
                        <div class="rh-filter-group">
                            <span class="rh-filter-label">Hasta</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="rh-filter-input">
                        </div>
                        <div style="display:flex; gap:8px; align-items:flex-end;">
                            <button type="submit" class="rh-btn rh-btn-primary">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Filtrar
                            </button>
                            @if(request()->hasAny(['search','start_date','end_date']))
                                <a href="{{ route('rh.index') }}" class="rh-btn rh-btn-outline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Limpiar
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Barra de exportaciones --}}
                <div class="rh-export-bar">
                    <span style="font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Exportar:</span>
                    <a href="{{ route('rh.export', ['search' => request('search'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                        class="rh-btn rh-btn-export-blue rh-btn-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Libro Mayor
                    </a>
                    <a href="{{ route('rh.exportWeek') }}" class="rh-btn rh-btn-export-green rh-btn-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Semana Nominal
                    </a>
                    <form action="{{ route('permissions.download-zip') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="search"     value="{{ request('search') }}">
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date"   value="{{ request('end_date') }}">
                        <button type="submit" class="rh-btn rh-btn-export-rose rh-btn-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            ZIP
                        </button>
                    </form>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    @if($permisos->isEmpty())
                        <div class="rh-empty">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p>No hay permisos para mostrar con los filtros aplicados.</p>
                        </div>
                    @else
                        <table class="rh-table">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th style="text-align:right;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permisos as $permiso)
                                    <tr>
                                        <td>
                                            <div style="display:flex; align-items:center; gap:10px;">
                                                @if($permiso->user && $permiso->user->foto_perfil)
                                                    <img src="{{ asset('storage/' . $permiso->user->foto_perfil) }}" class="rh-avatar" alt="">
                                                @else
                                                    <div class="rh-avatar-ph">{{ strtoupper(substr($permiso->user->name ?? 'N', 0, 1)) }}</div>
                                                @endif
                                                <div>
                                                    <p style="font-weight:600; font-size:13px; line-height:1.3;">{{ $permiso->user->name ?? 'N/A' }}</p>
                                                    <p style="font-size:11px; color:var(--muted);">{{ $permiso->department->name ?? '' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($permiso->entry_exit_type === 'entrada')
                                                <span class="rh-tipo-tag rh-tipo-entrada">↙ Entrada</span>
                                            @else
                                                <span class="rh-tipo-tag rh-tipo-salida">↗ Salida</span>
                                            @endif
                                        </td>
                                        <td style="font-size:13px; color:var(--muted); white-space:nowrap;">
                                            {{ \Carbon\Carbon::parse($permiso->date)->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <span class="rh-badge rh-badge-{{ $permiso->status }}">
                                                <span class="rh-dot"></span>
                                                {{ ucfirst($permiso->status) }}
                                            </span>
                                        </td>
                                        <td style="text-align:right;">
                                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                                <a href="{{ route('permissions.show', $permiso->id) }}" class="rh-btn rh-btn-gold rh-btn-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    Ver
                                                </a>
                                                <a href="{{ route('permissions.download', $permiso->id) }}" class="rh-btn rh-btn-brown rh-btn-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="rh-footer">
                    <p style="font-size:13px; color:var(--muted);">{{ count($permisos) }} registro(s) encontrados</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>