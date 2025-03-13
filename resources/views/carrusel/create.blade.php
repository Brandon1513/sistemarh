<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Administrar Carrusel') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Título del formulario -->
                    <h2 class="mb-6 text-lg font-bold text-gray-700">{{ __('Nueva Imagen para el Carrusel') }}</h2>

                    <!-- Formulario de Registro -->
                    <form method="POST" action="{{ route('carrusel.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Imagen -->
                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Imagen del Carrusel')" />
                            <input type="file" id="image" name="image" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <!-- Título de la Imagen -->
                        <div class="mb-4">
                            <x-input-label for="titulo" :value="__('Título')" />
                            <x-text-input id="titulo" class="block w-full mt-1" type="text" name="titulo" :value="old('titulo')" required />
                            <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                        </div>

                        <!-- Descripción de la Imagen -->
                        <div class="mb-4">
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <textarea id="descripcion" name="descripcion" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>{{ old('descripcion') }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <!-- Botón de subir imagen -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Subir Imagen') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

