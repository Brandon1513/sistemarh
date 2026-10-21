<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="//unpkg.com/alpinejs" defer></script>

        {{-- Favicon --}}
        <link rel="icon" href="{{ asset('images/favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon-192x192.png') }}">
        <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('images/favicon-512x512.png') }}">
        <meta name="theme-color" content="#6A2C75">

    </head>
        <script>
        // Evita envíos dobles: al enviar un formulario POST, deshabilita sus botones.
        document.addEventListener('submit', function (e) {
            const form = e.target;

            // Si un confirm() del formulario canceló el envío, no hacemos nada
            if (e.defaultPrevented) return;

            // Los filtros (GET) se pueden enviar las veces que se quiera
            if ((form.getAttribute('method') || 'get').toLowerCase() !== 'post') return;

            // Ya se está enviando: ignorar el segundo clic
            if (form.dataset.enviando === '1') {
                e.preventDefault();
                return;
            }

            form.dataset.enviando = '1';
            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (btn) {
                btn.dataset.textoOriginal = btn.innerHTML;
                btn.innerHTML = 'Enviando…';
                btn.disabled = true;
            });
        });

        // Si el usuario regresa con el botón "Atrás", el navegador puede mostrar la página
        // guardada con los botones deshabilitados. Aquí los reactivamos.
        window.addEventListener('pageshow', function () {
            document.querySelectorAll('form[data-enviando="1"]').forEach(function (form) {
                form.dataset.enviando = '';
                form.querySelectorAll('[data-texto-original]').forEach(function (btn) {
                    btn.innerHTML = btn.dataset.textoOriginal;
                    btn.disabled = false;
                });
            });
        });
    </script>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
    <script src="//unpkg.com/alpinejs" defer></script>
</html>
