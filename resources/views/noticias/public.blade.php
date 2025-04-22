<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Noticias') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-center bg-cover" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h1 class="text-3xl font-bold text-center">Últimas Noticias</h1>
                <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($noticias as $noticia)
                        <div class="overflow-hidden bg-white rounded-lg shadow-md cursor-pointer" onclick="openModal({{ $noticia->id }})">
                            <img src="{{ $noticia->imagen ? asset('storage/' . $noticia->imagen) : asset('images/placeholder.jpg') }}" class="object-cover w-full h-48">
                            <div class="p-4">
                                <h2 class="text-xl font-semibold">{{ $noticia->titulo }}</h2>
                                <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit(strip_tags($noticia->contenido), 100, '...') }}</p>
                                <p class="mt-2 text-sm text-gray-500">{{ $noticia->fecha }}</p>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div 
                            id="modal-{{ $noticia->id }}" 
                            x-data="{ showLightbox: false, selectedImage: '' }"
                            class="fixed inset-0 z-50 flex items-center justify-center hidden overflow-y-auto bg-gray-900 bg-opacity-50"
                        >
                            <div class="relative w-full max-w-4xl mx-auto bg-white rounded-lg shadow-lg">
                                <div class="flex justify-between p-5 border-b">
                                    <h2 class="text-2xl font-bold">{{ $noticia->titulo }}</h2>
                                    <button class="text-gray-500 hover:text-gray-800" onclick="closeModal({{ $noticia->id }})">✖</button>
                                </div>
                                <div class="p-5">
                                    <p class="text-sm text-gray-500">{{ $noticia->fecha }} | Autor: <strong>{{ $noticia->autor }}</strong></p>

                                    <!-- Imagen de portada -->
                                    <img src="{{ asset('storage/' . $noticia->imagen) }}" class="object-cover w-full mt-4 rounded-lg">

                                    <!-- Galería -->
                                    @if ($noticia->galeria->count())
                                        <div class="mt-4">
                                            <h3 class="mb-2 text-lg font-semibold">Galería</h3>
                                            <div class="flex flex-wrap gap-3">
                                                @foreach ($noticia->galeria as $imagen)
                                                    <img 
                                                        src="{{ asset('storage/' . $imagen->imagen) }}" 
                                                        @click="selectedImage = '{{ asset('storage/' . $imagen->imagen) }}'; showLightbox = true" 
                                                        class="object-cover w-32 h-32 rounded cursor-pointer hover:scale-105 transition-transform"
                                                        alt="Imagen de galería">
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Lightbox -->
                                    <div 
                                        x-show="showLightbox" 
                                        x-transition 
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80"
                                        x-cloak
                                    >
                                        <div class="relative max-w-4xl p-4">
                                            <button 
                                                class="absolute top-0 right-0 text-white text-3xl" 
                                                @click="showLightbox = false"
                                            >
                                                &times;
                                            </button>
                                            <img :src="selectedImage" class="max-w-full max-h-screen rounded shadow-lg">
                                        </div>
                                    </div>

                                    <!-- Contenido -->
                                    <p class="mt-4 text-gray-700">{!! $noticia->contenido !!}</p>

                                    <!-- Multimedia -->
                                    @if($noticia->multimedia)
                                        <p class="mt-2"><strong>Multimedia:</strong> 
                                            <a href="{{ $noticia->multimedia }}" target="_blank" class="text-blue-600 hover:underline">Ver Multimedia</a>
                                        </p>
                                    @endif
                                </div>

                                <!-- Botón cerrar -->
                                <div class="p-5 border-t">
                                    <button class="px-4 py-2 text-white bg-gray-600 rounded-md hover:bg-gray-700" onclick="closeModal({{ $noticia->id }})">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-6">
                    {{ $noticias->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openModal(id) {
            document.getElementById('modal-' + id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).classList.add('hidden');
        }
    </script>
</x-app-layout>
