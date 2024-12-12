@component('mail::message')
# Exportación Libro Mayor Completada

La exportación de Libro Mayor de permisos ha sido completada. Puedes descargar el archivo desde el siguiente enlace:

@component('mail::button', ['url' => $url])
Descargar Permisos Libro Mayor
@endcomponent

Si no solicitaste esta exportación, por favor ignora este mensaje.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
