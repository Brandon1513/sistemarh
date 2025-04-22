
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-white">
            {{ __('Crear Noticia') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-center bg-cover" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 overflow-hidden bg-white shadow-md sm:rounded-lg">
                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>⚠️ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('noticias.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Título -->
                    <div>
                        <label class="block font-medium text-gray-700">Título</label>
                        <input type="text" name="titulo" class="w-full px-4 py-2 mt-1 border rounded-md" required>
                    </div>

                    <!-- Contenido -->
                    <div class="mt-4">
                        <label class="block font-medium text-gray-700">Contenido</label>
                        <textarea name="contenido" id="contenido" rows="5" class="w-full px-4 py-2 mt-1 border rounded-md" required></textarea>
                    </div>

                    <!-- Imagen principal -->
                    <div class="mt-4">
                        <label class="block font-medium text-gray-700">Imagen Principal (Portada)</label>
                        <input type="file" name="imagen" accept="image/*" class="w-full px-4 py-2 border rounded-md" required>
                    </div>

                    <!-- Galería -->
                    <div class="mt-4">
                        <label class="block font-medium text-gray-700">Galería de Imágenes (Opcional)</label>
                        <input type="file" name="galeria[]" accept="image/*" multiple class="w-full px-4 py-2 border rounded-md">
                        <small class="text-sm text-gray-500">Puedes seleccionar múltiples imágenes.</small>
                    </div>

                    <!-- Video (URL de YouTube o repositorio) -->
                    <div class="mt-4">
                        <label for="multimedia" class="block font-medium text-gray-700">Video (Opcional - URL)</label>
                        <input type="url" name="multimedia" id="multimedia" class="w-full px-4 py-2 mt-1 border rounded-md">
                    </div>

                    <!-- Autor -->
                    <div class="mt-4">
                        <label class="block font-medium text-gray-700">Autor</label>
                        <input type="text" name="autor" class="w-full px-4 py-2 mt-1 border rounded-md" required>
                    </div>

                    <!-- Fecha -->
                    <div class="mt-4">
                        <label class="block font-medium text-gray-700">Fecha</label>
                        <input type="date" name="fecha" class="w-full px-4 py-2 mt-1 border rounded-md" required>
                    </div>

                    <!-- Estado Activo -->
                    <!-- Input oculto para establecer el estado en "Activo" automáticamente -->
                    <input type="hidden" name="activo" value="1">

                    <!-- Botón de Guardar -->
                    <!-- Botones de acción -->
                    <div class="flex justify-center mt-6 space-x-4">
                        <!-- Botón de Registrar -->
                        <x-primary-button class="px-6 py-2 text-white bg-purple-600 hover:bg-purple-700 focus:bg-purple-800 active:bg-purple-900">
                            {{ __('Guardar') }}
                        </x-primary-button>

                        <!-- Botón de Cancelar -->
                        <x-primary-button class="px-6 py-2 text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:bg-yellow-800 active:bg-yellow-900">
                            <a href="{{ route('noticias.index') }}">{{ __('Cancelar') }}</a>
                        </x-primary-button>
                    </div>


                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<head>
    <script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            CKEDITOR.replace('contenido');
        });
    </script>
</head>
