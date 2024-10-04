@component('mail::message')
# Nueva Solicitud de Permiso

Hola, {{ $permiso->empleado->supervisor->name }},

El empleado **{{ $permiso->empleado->name }}** ha solicitado un permiso con los siguientes detalles:

- **Departamento:** {{ $permiso->departamento->name }}
- **Fecha de Inicio:** {{ $permiso->fecha_inicio }}
- **Fecha de Término:** {{ $permiso->fecha_termino }}
- **Motivo:** {{ $permiso->motivo }}
- **Tipo de Permiso:** {{ $permiso->tipo_permiso }}

Por favor, ingresa al sistema para aprobar o rechazar esta solicitud.

@component('mail::button', ['url' => route('permisos.show', $permiso->id)])
Revisar Solicitud
@endcomponent

Gracias por usar nuestra aplicación.

Saludos,<br>
{{ config('app.name') }}
@endcomponent
