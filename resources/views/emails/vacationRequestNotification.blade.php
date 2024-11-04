@component('mail::message')
# Nueva Solicitud de Vacaciones

El empleado {{ $employee->name }} ha solicitado vacaciones.

**Fechas de vacaciones:**
- **Fecha de Inicio de Vacaciones:** {{ \Carbon\Carbon::parse($vacationRequest->fecha_inicio)->format('d/m/Y') }}
- **Fecha de Término de Vacaciones:** {{ \Carbon\Carbon::parse($vacationRequest->fecha_fin)->format('d/m/Y') }}
- **Fecha de Reincorporación:** {{ \Carbon\Carbon::parse($vacationRequest->fecha_reincorporacion)->format('d/m/Y') }}
- **Días Solicitados:** {{ $vacationRequest->dias_solicitados }}

@component('mail::button', ['url' => route('vacaciones.show', $vacationRequest->id)])
Ver Solicitud de Vacaciones
@endcomponent

@component('mail::button', ['url' => route('vacaciones.aprobar', $vacationRequest->id)])
Aprobar
@endcomponent

@component('mail::button', ['url' => route('vacaciones.rechazar', $vacationRequest->id)])
Rechazar
@endcomponent

Gracias por usar nuestra aplicación.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
