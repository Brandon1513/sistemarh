<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Descargar App') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Obtén la app móvil que desarrollamos para los colaboradores de la empresa.') }}
        </p>
    </header>

    <div class="max-w-md p-6 mt-6 text-center bg-purple-100 rounded-lg shadow-md">
        <!-- Logo -->
        <img src="{{ asset('images/logo.png') }}" alt="Logo de la empresa" class="w-20 h-20 mx-auto mb-4 rounded-full shadow">

        <!-- Nombre de la app -->
        <h3 class="mb-2 text-xl font-bold text-purple-800">App de Colaboradores</h3>

        <!-- Descripción -->
        <p class="mb-4 text-sm text-gray-700">
            Accede a funciones útiles y mantente al tanto de las novedades directamente desde tu teléfono.
        </p>

        <!-- Botón de descarga -->
        <a href="{{ asset('apk/dasavena-1.apk') }}" download
           class="inline-block px-4 py-2 text-white transition bg-purple-600 rounded hover:bg-purple-700">
            📱 Descargar APK
        </a>
    </div>
</section>
