@component('mail::message')
# Exportación Semanal Completada

La exportación semanal de permisos ha sido completada. Puedes descargar el archivo desde el siguiente enlace:

@component('mail::button', ['url' => $url])
Descargar Permisos Semanales
@endcomponent

Si no solicitaste esta exportación, por favor ignora este mensaje.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
