@php
    $employee   = $permiso->empleado;
    $supervisor = $employee?->supervisor;

    $fmtDate = fn ($d) => $d ? \Carbon\Carbon::parse($d)->locale('es')->translatedFormat('d \d\e F \d\e Y') : '—';
    $fmtDay  = fn ($d) => $d ? ucfirst(\Carbon\Carbon::parse($d)->locale('es')->translatedFormat('l')) : '';

    // Días naturales que abarca la ausencia (incluye inicio y término)
    $days = ($permiso->fecha_inicio && $permiso->fecha_termino)
        ? (int) abs(\Carbon\Carbon::parse($permiso->fecha_inicio)->startOfDay()->diffInDays(\Carbon\Carbon::parse($permiso->fecha_termino)->startOfDay())) + 1
        : null;

    $types    = ['Permiso', 'Comisión', 'Suspensión'];
    $withPay  = $permiso->tipo_permiso === 'Con Goce de Sueldo';
    $noPay    = $permiso->tipo_permiso === 'Sin Goce de Sueldo';
@endphp

@extends('pdf.layout', [
    'docTitle'    => 'Control de Ausencias del Personal',
    'docSubtitle' => 'Solicitud de permiso, comisión o suspensión',
    'docCode'     => 'F-REH-36',
    'docRevision' => '03',
    'docIssued'   => '17-jun-2024',
    'folio'       => 'AU-' . str_pad($permiso->id, 6, '0', STR_PAD_LEFT),
    'status'      => $permiso->estado,
    'requestedAt' => $permiso->created_at,
])

@section('content')

    {{-- Datos del colaborador --}}
    <div class="section">
        <div class="section-title">Datos del colaborador</div>
        <table class="fields">
            <tr>
                <td class="label">Nombre completo</td>
                <td class="value">{{ $employee?->name ?? '—' }}</td>
                <td class="label">No. de empleado</td>
                <td class="value">{{ $employee?->clave_empleado ?: '—' }}</td>
            </tr>
            <tr>
                <td class="label">Puesto</td>
                <td class="value">{{ $employee?->puesto_empleado ?: '—' }}</td>
                <td class="label">Departamento</td>
                <td class="value">{{ $permiso->departamento?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Jefe inmediato</td>
                <td class="value">{{ $supervisor?->name ?? '—' }}</td>
                <td class="label">Fecha de ingreso</td>
                <td class="value">{{ $employee?->fecha_ingreso ? $fmtDate($employee->fecha_ingreso) : '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- Tipo de ausencia --}}
    <div class="section">
        <div class="section-title">Tipo de ausencia</div>
        <table class="options">
            <tr>
                @foreach ($types as $type)
                    @php $on = $permiso->tipo === $type; @endphp
                    <td class="{{ $on ? 'on' : '' }}" style="width:33.33%;">
                        <span class="box {{ $on ? 'checked' : '' }}"></span> {{ $type }}
                    </td>
                @endforeach
            </tr>
        </table>
        <table class="options" style="margin-top:-1px;">
            <tr>
                <td class="{{ $withPay ? 'on' : '' }}" style="width:50%;">
                    <span class="box {{ $withPay ? 'checked' : '' }}"></span> Con goce de sueldo
                </td>
                <td class="{{ $noPay ? 'on' : '' }}" style="width:50%;">
                    <span class="box {{ $noPay ? 'checked' : '' }}"></span> Sin goce de sueldo
                </td>
            </tr>
        </table>
    </div>

    {{-- Periodo --}}
    <div class="section">
        <div class="section-title">Periodo de ausencia</div>
        <table class="stats" style="margin-bottom: 8px;">
            <tr>
                <td style="width:25%;">
                    <div class="stat">
                        <div class="cap">Inicio</div>
                        <div style="font-weight:bold; font-size:10px; color:#6A2C75;">{{ $fmtDate($permiso->fecha_inicio) }}</div>
                        <div class="cap">{{ $fmtDay($permiso->fecha_inicio) }}</div>
                    </div>
                </td>
                <td style="width:25%;">
                    <div class="stat">
                        <div class="cap">Término</div>
                        <div style="font-weight:bold; font-size:10px; color:#6A2C75;">{{ $fmtDate($permiso->fecha_termino) }}</div>
                        <div class="cap">{{ $fmtDay($permiso->fecha_termino) }}</div>
                    </div>
                </td>
                <td style="width:25%;">
                    <div class="stat">
                        <div class="cap">Regresa a laborar</div>
                        <div style="font-weight:bold; font-size:10px; color:#6A2C75;">{{ $fmtDate($permiso->fecha_regreso_laborar) }}</div>
                        <div class="cap">{{ $fmtDay($permiso->fecha_regreso_laborar) }}</div>
                    </div>
                </td>
                <td style="width:25%;">
                    <div class="stat gold">
                        <div class="cap">Días naturales</div>
                        <div class="num">{{ $days ?? '—' }}</div>
                        <div class="cap">de ausencia</div>
                    </div>
                </td>
            </tr>
        </table>
        <table class="fields">
            <tr>
                <td class="label">Día de descanso</td>
                <td class="value">{{ $permiso->dia_descanso ?: '—' }}</td>
                <td class="label">Documento soporte</td>
                <td class="value">{{ $permiso->archivo ? 'Sí, adjunto en la Intranet' : 'No se adjuntó' }}</td>
            </tr>
        </table>
    </div>

    {{-- Observaciones --}}
    <div class="section">
        <div class="section-title">Motivo / Observaciones</div>
        <div class="note">{{ $permiso->motivo ?: 'Sin observaciones.' }}</div>
    </div>

    {{-- Firmas --}}
    <table class="signatures">
        <tr>
            <td>
                <div class="sign-status">&nbsp;</div>
                <div class="sign-line">
                    <div class="sign-name">{{ $employee?->name ?? '' }}</div>
                    <div class="sign-role">Colaborador (nombre y firma)</div>
                </div>
            </td>
            <td>
                <div class="sign-status muted">{{ ucfirst($permiso->estado ?? 'pendiente') }}</div>
                <div class="sign-line">
                    <div class="sign-name">{{ $supervisor?->name ?? '' }}</div>
                    <div class="sign-role">Jefe inmediato</div>
                </div>
            </td>
            <td>
                <div class="sign-status">&nbsp;</div>
                <div class="sign-line">
                    <div class="sign-name">&nbsp;</div>
                    <div class="sign-role">Recursos Humanos</div>
                </div>
            </td>
        </tr>
    </table>

@endsection
