@php
    $employee   = $permission->user;
    $supervisor = $employee?->supervisor;
    $isEntry    = $permission->entry_exit_type === 'entrada';
    $isExit     = $permission->entry_exit_type === 'salida';

    $fmtDate = fn ($d) => $d ? \Carbon\Carbon::parse($d)->locale('es')->translatedFormat('l d \d\e F \d\e Y') : '—';
    $fmtTime = fn ($t) => $t ? \Carbon\Carbon::parse($t)->format('h:i A') : '—';

    $status = $permission->status;
@endphp

@extends('pdf.layout', [
    'docTitle'    => 'Pase de Entrada / Salida',
    'docSubtitle' => 'Autorización para entrar o salir fuera del horario oficial',
    'docCode'     => 'F-REH-02',
    'docRevision' => '05',
    'docIssued'   => '17-jun-2024',
    'folio'       => 'PS-' . str_pad($permission->id, 6, '0', STR_PAD_LEFT),
    'status'      => $status,
    'requestedAt' => $permission->created_at,
])

@section('content')

    {{-- Tipo de pase --}}
    <div class="section">
        <div class="section-title">Tipo de pase</div>
        <table class="options">
            <tr>
                <td class="{{ $isEntry ? 'on' : '' }}" style="width:50%;">
                    <span class="box {{ $isEntry ? 'checked' : '' }}"></span> Pase de entrada
                </td>
                <td class="{{ $isExit ? 'on' : '' }}" style="width:50%;">
                    <span class="box {{ $isExit ? 'checked' : '' }}"></span> Pase de salida
                </td>
            </tr>
        </table>
    </div>

    {{-- Datos del colaborador --}}
    <div class="section">
        <div class="section-title">Datos del colaborador</div>
        <table class="fields">
            <tr>
                <td class="label">Nombre</td>
                <td class="value">{{ $employee?->name ?? '—' }}</td>
                <td class="label">No. de empleado</td>
                <td class="value">{{ $employee?->clave_empleado ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Puesto</td>
                <td class="value">{{ $permission->position ?: ($employee?->puesto_empleado ?: '—') }}</td>
                <td class="label">Departamento</td>
                <td class="value">{{ $permission->department?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Jefe inmediato</td>
                <td class="value">{{ $supervisor?->name ?? '—' }}</td>
                <td class="label">Correo</td>
                <td class="value" style="font-weight:normal;">{{ $employee?->email ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- Detalle del pase --}}
    <div class="section">
        <div class="section-title">Detalle del pase</div>
        <table class="fields">
            <tr>
                <td class="label">Fecha del pase</td>
                <td class="value" colspan="3">{{ ucfirst($fmtDate($permission->date)) }}</td>
            </tr>
            <tr>
                <td class="label">Horario oficial</td>
                <td class="value">{{ $fmtTime($permission->official_schedule) }}</td>
                <td class="label">Hora de {{ $isEntry ? 'entrada' : ($isExit ? 'salida' : 'entrada / salida') }}</td>
                <td class="value">{{ $fmtTime($permission->entry_exit_time) }}</td>
            </tr>
            <tr>
                <td class="label">Documento soporte</td>
                <td class="value" colspan="3">{{ $permission->supporting_document ? 'Sí, adjunto en la Intranet' : 'No se adjuntó documento' }}</td>
            </tr>
        </table>
    </div>

    {{-- Motivo --}}
    <div class="section">
        <div class="section-title">Motivo</div>
        <div class="note">{{ $permission->reason ?: 'Sin motivo registrado.' }}</div>
    </div>

    {{-- Seguimiento --}}
    <div class="section">
        <div class="section-title">Seguimiento</div>
        <table class="fields">
            <tr>
                <td class="label">Autorizó</td>
                <td class="value">{{ $permission->approver?->name ?? 'Pendiente' }}</td>
                <td class="label">Recursos Humanos</td>
                <td class="value">{{ $permission->hr?->name ?? 'Pendiente' }}</td>
            </tr>
        </table>
    </div>

    {{-- Firmas --}}
    <table class="signatures">
        <tr>
            <td>
                <div class="sign-status">&nbsp;</div>
                <div class="sign-line">
                    <div class="sign-name">{{ $employee?->name ?? '' }}</div>
                    <div class="sign-role">Firma del colaborador</div>
                </div>
            </td>
            <td>
                <div class="sign-status muted">{{ ucfirst($status ?? 'pendiente') }}</div>
                <div class="sign-line">
                    <div class="sign-name">{{ $permission->approver?->name ?? $supervisor?->name ?? '' }}</div>
                    <div class="sign-role">Jefe inmediato</div>
                </div>
            </td>
            <td>
                <div class="sign-status">&nbsp;</div>
                <div class="sign-line">
                    <div class="sign-name">{{ $permission->hr?->name ?? '' }}</div>
                    <div class="sign-role">Recursos Humanos</div>
                </div>
            </td>
        </tr>
    </table>

@endsection
