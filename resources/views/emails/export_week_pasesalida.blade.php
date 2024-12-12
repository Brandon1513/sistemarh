@component('mail::message')
# Exportación Semanal Pases de Salida Completada

La exportación semanal de pases de salida ha sido completada. Puedes descargar el archivo desde el siguiente enlace:

@component('mail::button', ['url' => $url])
Descargar Pases de Salida Semanales
@endcomponent

Si no solicitaste esta exportación, por favor ignora este mensaje.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
