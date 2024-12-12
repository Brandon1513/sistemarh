@component('mail::message')
# Tu archivo ZIP está listo para descargar

Puedes descargar el archivo ZIP con los permisos solicitados desde el siguiente enlace:

@component('mail::button', ['url' => $downloadLink])
Descargar ZIP
@endcomponent

Gracias,  
{{ config('app.name') }}
@endcomponent
