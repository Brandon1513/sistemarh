<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Agregar Recursos Humanos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('recursoshumanos.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-label for="name" :value="__('Nombre Completo')" />
                            <x-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required autofocus />
                        </div>

                        <div class="mb-4">
                            <x-label for="email" :value="__('Correo Electrónico')" />
                            <x-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required />
                        </div>

                        <div class="mb-4">
                            <x-label for="password" :value="__('Contraseña')" />
                            <x-input id="password" class="block w-full mt-1" type="password" name="password" required />
                        </div>

                        <div class="mb-4">
                            <x-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                            <x-input id="password_confirmation" class="block w-full mt-1" type="password" name="password_confirmation" required />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button class="ml-4">
                                {{ __('Guardar') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
