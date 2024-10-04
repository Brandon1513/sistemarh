@component('mail::message')
# Hola, {{ $user->name }}

Te damos la bienvenida a nuestra plataforma. Tus credenciales son:

- **Usuario:** {{ $user->email }}
- **Contraseña:** {{ $password }}

Te han sido asignados los siguientes roles:

@foreach ($roles as $role)
    - {{ $role->name }}
@endforeach


Te recomendamos cambiar tu contraseña en cuanto inicies sesión.

@component('mail::button', ['url' => route('login')])
Iniciar Sesión
@endcomponent

Gracias por usar nuestra aplicación.

Saludos,<br>
{{ config('app.name') }}
@endcomponent
