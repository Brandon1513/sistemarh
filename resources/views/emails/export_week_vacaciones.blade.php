@component('mail::message')
# Exportación Semanal Vacaciones Completada

La exportación semanal de vacaciones ha sido completada. Puedes descargar el archivo desde el siguiente enlace:

@component('mail::button', ['url' => $url])
Descargar Vacaciones Semanales
@endcomponent

Si no solicitaste esta exportación, por favor ignora este mensaje.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
