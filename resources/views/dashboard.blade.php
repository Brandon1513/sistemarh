<x-app-layout>
   

    <!-- Carrusel de imágenes -->
    <div id="carouselExample" class="relative w-full overflow-hidden  shadow-lg h-[500px] sm:h-[450px] md:h-[550px] lg:h-[600px]">
        <div class="relative w-full h-full carousel-inner">
            @foreach ($carruselImages as $index => $image)
                <div class="carousel-item absolute w-full h-full transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
                    <img src="{{ asset('storage/' . $image->image) }}" class="object-cover w-full h-full">
                    <div class="absolute inset-0 flex flex-col items-center justify-center px-6 text-center bg-black bg-opacity-40">
                        <h2 class="text-3xl font-extrabold text-white sm:text-4xl md:text-5xl">
                            {{ $image->titulo ?? 'Título del Carrusel' }}
                        </h2>
                        <p class="mt-2 text-lg text-gray-200 sm:text-xl md:text-2xl">
                            {{ $image->descripcion ?? 'Descripción breve de la imagen en el carrusel.' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <button id="prev" class="absolute px-3 py-2 text-white transform -translate-y-1/2 bg-black bg-opacity-50 rounded-full left-4 top-1/2 hover:bg-opacity-75">
            ‹
        </button>
        <button id="next" class="absolute px-3 py-2 text-white transform -translate-y-1/2 bg-black bg-opacity-50 rounded-full right-4 top-1/2 hover:bg-opacity-75">
            ›
        </button>
    </div>

    <!-- Noticias -->
    <!-- Sección de Noticias -->
<div class="py-12 bg-center bg-cover" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-6 bg-white shadow-md sm:rounded-lg">
            <div class="text-center">
                <h1 class="text-3xl font-bold">Últimas noticias</h1>
                <a href="{{ route('noticias.public') }}" class="font-semibold text-purple-700 hover:underline">Ver todas las noticias ></a>
            </div>

            <!-- Contenedor Desplazable de Noticias -->
            <div class="relative mt-6">
                <button id="prevNews" class="absolute left-0 z-10 px-3 py-2 text-white -translate-y-1/2 bg-gray-600 rounded-full top-1/2 hover:bg-gray-800">‹</button>
                
                <div id="newsCarousel" class="flex space-x-6 overflow-hidden">
                    @foreach ($noticias as $noticia)
                        <div class="flex-none w-[300px] bg-white rounded-lg shadow-md cursor-pointer" onclick="openModal({{ $noticia->id }})">
                            <img src="{{ $noticia->imagen ? asset('storage/'.$noticia->imagen) : asset('images/placeholder.jpg') }}" class="object-cover w-full h-48 rounded-t-lg" alt="Imagen de la noticia">
                            <div class="p-4" style="background-color: #C7B7CA;">
                                <h2 class="font-semibold truncate text-md">{{ $noticia->titulo }}</h2>
                                <p class="w-full overflow-hidden text-xs text-gray-700 text-ellipsis whitespace-nowrap">
                                    {{ Str::limit(strip_tags($noticia->contenido), 80, '...') }}
                                </p>
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($noticia->fecha)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button id="nextNews" class="absolute right-0 z-10 px-3 py-2 text-white -translate-y-1/2 bg-gray-600 rounded-full top-1/2 hover:bg-gray-800">›</button>
            </div>
        </div>
    </div>
</div>


    <!-- Modales para cada noticia -->
    @foreach ($noticias as $noticia)
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
                    <p id="short-text-{{ $noticia->id }}" class="mt-4 text-gray-700">
                        {!! Str::limit(strip_tags($noticia->contenido), 100, '...') !!}
                        @if(strlen(strip_tags($noticia->contenido)) > 100)
                            <button id="ver-mas-{{ $noticia->id }}" 
                                    onclick="expandText({{ $noticia->id }})" 
                                    class="text-blue-600 hover:underline">Ver más</button>
                        @endif
                    </p>

                    <p id="full-text-{{ $noticia->id }}" class="hidden mt-4 text-gray-700">
                        {!! strip_tags($noticia->contenido) !!}
                        <button id="ver-menos-{{ $noticia->id }}" 
                                onclick="collapseText({{ $noticia->id }})" 
                                class="block mt-2 text-blue-600 hover:underline">Ver menos</button>
                    </p>

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

    <!-- Scripts -->
    <script>
        function openModal(id) {
            document.getElementById('modal-' + id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).classList.add('hidden');
        }

        function expandText(id) {
            document.getElementById('short-text-' + id).classList.add('hidden'); // Oculta la versión corta
            document.getElementById('full-text-' + id).classList.remove('hidden'); // Muestra la versión completa
        }

        function collapseText(id) {
            document.getElementById('full-text-' + id).classList.add('hidden'); // Oculta la versión completa
            document.getElementById('short-text-' + id).classList.remove('hidden'); // Muestra la versión corta
        }

        document.addEventListener("DOMContentLoaded", function () {
    /*** Carrusel de imágenes automático ***/
    let index = 0;
    const items = document.querySelectorAll(".carousel-item");
    const total = items.length;

    function showSlide(newIndex) {
        items[index].classList.remove("opacity-100");
        items[index].classList.add("opacity-0");
        index = newIndex;
        items[index].classList.add("opacity-100");
    }

    document.getElementById("prev").addEventListener("click", function () {
        showSlide(index === 0 ? total - 1 : index - 1);
    });

    document.getElementById("next").addEventListener("click", function () {
        showSlide(index === total - 1 ? 0 : index + 1);
    });

    // Agregar desplazamiento automático cada 5 segundos
    setInterval(() => {
        showSlide(index === total - 1 ? 0 : index + 1);
    }, 5000);


    /*** Carrusel de Noticias con desplazamiento ***/
    const newsContainer = document.getElementById("newsCarousel");
    const prevButton = document.getElementById("prevNews");
    const nextButton = document.getElementById("nextNews");

    let scrollAmount = 0;
    const scrollStep = 320; // Ancho de una noticia + margen

    prevButton.addEventListener("click", function () {
        scrollAmount -= scrollStep;
        if (scrollAmount < 0) scrollAmount = 0;
        newsContainer.scrollTo({
            left: scrollAmount,
            behavior: "smooth"
        });
    });

    nextButton.addEventListener("click", function () {
        scrollAmount += scrollStep;
        if (scrollAmount > newsContainer.scrollWidth - newsContainer.clientWidth) {
            scrollAmount = newsContainer.scrollWidth - newsContainer.clientWidth;
        }
        newsContainer.scrollTo({
            left: scrollAmount,
            behavior: "smooth"
        });
    });
});


    </script>
   
</x-app-layout>
