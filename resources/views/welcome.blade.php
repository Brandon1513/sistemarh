<x-app-layout>
    <div class="py-16 overflow-hidden bg-gray-50 lg:py-24">
        <div class="max-w-xl px-4 mx-auto sm:px-6 lg:px-8 lg:max-w-7xl">
            <div class="relative">
                <!-- Título Principal -->
                <h2 class="text-4xl font-extrabold leading-8 tracking-tight text-center text-indigo-600 sm:text-6xl">
                    Bienvenido a Dasavena RH
                </h2>
                <!-- Descripción -->
                <p class="max-w-3xl mx-auto mt-4 text-xl text-center text-gray-500">
                    Encuentra información de apoyo, involucrate en los procesos administrativos, se parte de Dasavena.
                    <span>Gracias por usar nuestra aplicación.</span>
                </p>
                
                <!-- Enlace de inicio de sesión -->
                <div class="mt-8 text-center">
                    <a href="{{ route('login') }}" class="inline-block px-8 py-3 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                        Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>