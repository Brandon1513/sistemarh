@php
    $employee   = $vacation->empleado;
    $supervisor = $employee?->supervisor;

    $fmtDate = fn ($d) => $d ? \Carbon\Carbon::parse($d)->locale('es')->translatedFormat('d \d\e F \d\e Y') : '—';
    $fmtDay  = fn ($d) => $d ? ucfirst(\Carbon\Carbon::parse($d)->locale('es')->translatedFormat('l')) : '';

    // Antigüedad a la fecha de inicio de las vacaciones
    $seniority = null;
    if ($employee?->fecha_ingreso) {
        $ref = $vacation->fecha_inicio ? \Carbon\Carbon::parse($vacation->fecha_inicio) : now();
        $diff = \Carbon\Carbon::parse($employee->fecha_ingreso)->diff($ref);
        $seniority = $diff->invert ? null : $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años')
            . ($diff->m ? ', ' . $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses') : '');
    }
@endphp

@extends('pdf.layout', [
    'docTitle'    => 'Solicitud de Vacaciones',
    'docSubtitle' => 'Programación de periodo vacacional',
    'folio'       => 'VAC-' . str_pad($vacation->id, 6, '0', STR_PAD_LEFT),
    'status'      => $vacation->estado,
    'requestedAt' => $vacation->fecha_solicitud ?? $vacation->created_at,
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
                <td class="value">{{ $vacation->departamento?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Jefe inmediato</td>
                <td class="value">{{ $supervisor?->name ?? '—' }}</td>
                <td class="label">Fecha de ingreso</td>
                <td class="value">{{ $employee?->fecha_ingreso ? $fmtDate($employee->fecha_ingreso) : '—' }}</td>
            </tr>
            <tr>
                <td class="label">Antigüedad</td>
                <td class="value">{{ $seniority ?? '—' }}</td>
                <td class="label">Periodo vacacional</td>
                <td class="value">{{ $vacation->periodo_correspondiente ?: '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- Saldo de días --}}
    <div class="section">
        <div class="section-title">Saldo de días</div>
        <table class="stats">
            <tr>
                <td style="width:33.33%;">
                    <div class="stat">
                        <div class="cap">Días que corresponden</div>
                        <div class="num">{{ $vacation->dias_corresponden ?? '—' }}</div>
                    </div>
                </td>
                <td style="width:33.33%;">
                    <div class="stat gold">
                        <div class="cap">Días solicitados</div>
                        <div class="num">{{ $vacation->dias_solicitados ?? '—' }}</div>
                    </div>
                </td>
                <td style="width:33.33%;">
                    <div class="stat">
                        <div class="cap">Días pendientes</div>
                        <div class="num">{{ $vacation->dias_pendientes ?? '—' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Fechas --}}
    <div class="section">
        <div class="section-title">Fechas del periodo</div>
        <table class="stats">
            <tr>
                <td style="width:33.33%;">
                    <div class="stat">
                        <div class="cap">Inicio</div>
                        <div style="font-weight:bold; font-size:10px; color:#6A2C75;">{{ $fmtDate($vacation->fecha_inicio) }}</div>
                        <div class="cap">{{ $fmtDay($vacation->fecha_inicio) }}</div>
                    </div>
                </td>
                <td style="width:33.33%;">
                    <div class="stat">
                        <div class="cap">Fin</div>
                        <div style="font-weight:bold; font-size:10px; color:#6A2C75;">{{ $fmtDate($vacation->fecha_fin) }}</div>
                        <div class="cap">{{ $fmtDay($vacation->fecha_fin) }}</div>
                    </div>
                </td>
                <td style="width:33.33%;">
                    <div class="stat">
                        <div class="cap">Reincorporación</div>
                        <div style="font-weight:bold; font-size:10px; color:#6A2C75;">{{ $fmtDate($vacation->fecha_reincorporacion) }}</div>
                        <div class="cap">{{ $fmtDay($vacation->fecha_reincorporacion) }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="note muted" style="min-height:0; font-size:8px;">
        El colaborador se compromete a reincorporarse a sus labores en la fecha indicada. Los días autorizados se
        descuentan del saldo del periodo vacacional correspondiente.
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
                <div class="sign-status muted">{{ ucfirst($vacation->estado ?? 'pendiente') }}</div>
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
