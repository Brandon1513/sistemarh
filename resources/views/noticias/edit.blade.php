<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Noticia') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">

                <!-- Mensajes de error -->
                @if ($errors->any())
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        <strong>Oops!</strong> Hay errores en el formulario.
                        <ul class="mt-2 text-red-600 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form method="POST" action="{{ route('noticias.update', $noticia->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Título -->
                    <div>
                        <x-input-label for="titulo" :value="__('Título')" />
                        <x-text-input id="titulo" class="block w-full mt-1" type="text" name="titulo" value="{{ old('titulo', $noticia->titulo) }}" required />
                    </div>

                    <!-- Contenido -->
                    <div class="mt-4">
                        <x-input-label for="contenido" :value="__('Contenido')" />
                        <textarea id="contenido" name="contenido" id="contenido" class="block w-full mt-1" rows="6">{{ old('contenido', $noticia->contenido) }}</textarea>
                    </div>

                    <!-- Imagen -->
                    <div class="mt-4">
                        <x-input-label for="imagen" :value="__('Imagen (Opcional)')" />
                        <input type="file" name="imagen" class="block w-full mt-1">
                        @if($noticia->imagen)
                            <p class="mt-1 text-sm text-gray-500">Imagen actual: 
                                <a href="{{ asset('storage/' . $noticia->imagen) }}" target="_blank" class="text-blue-600 hover:underline">
                                    Ver imagen
                                </a>
                            </p>
                        @endif
                    </div>

                    <!-- Video -->
                    <div class="mt-4">
                        <x-input-label for="multimedia" :value="__('Video (Opcional)')" />
                        <x-text-input id="multimedia" class="block w-full mt-1" type="text" name="multimedia" value="{{ old('multimedia', $noticia->multimedia) }}" placeholder="URL de YouTube" />
                    </div>


                    <!-- Estado -->
                    <input type="hidden" name="activo" value="1"> 
                    <!-- Autor -->
                    <div class="mt-4">
                        <x-input-label for="autor" :value="__('Autor')" />
                        <x-text-input id="autor" class="block w-full mt-1" type="text" name="autor" value="{{ old('autor', $noticia->autor) }}" required />
                    </div>

                    <!-- Fecha -->
                    <div class="mt-4">
                        <x-input-label for="fecha" :value="__('Fecha')" />
                        <x-text-input id="fecha" class="block w-full mt-1" type="date" name="fecha" value="{{ old('fecha', $noticia->fecha) }}" required />
                    </div>



                    <!-- Botones -->
                    <div class="flex justify-end mt-6 space-x-4">
                        <a href="{{ route('noticias.index') }}" class="px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700">Cancelar</a>
                        <x-primary-button>
                            {{ __('Actualizar Noticia') }}
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