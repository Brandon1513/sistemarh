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

                        <!-- Modal para cada noticia -->
                        <div id="modal-{{ $noticia->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900 bg-opacity-50">
                            <div class="relative w-full max-w-4xl mx-auto mt-20 bg-white rounded-lg shadow-lg">
                                <div class="flex justify-between p-5 border-b">
                                    <h2 class="text-2xl font-bold">{{ $noticia->titulo }}</h2>
                                    <button class="text-gray-500 hover:text-gray-800" onclick="closeModal({{ $noticia->id }})">✖</button>
                                </div>
                                <div class="p-5">
                                    <p class="text-sm text-gray-500">{{ $noticia->fecha }} | Autor: <strong>{{ $noticia->autor }}</strong></p>
                                    <img src="{{ asset('storage/' . $noticia->imagen) }}" class="object-cover w-full mt-4 rounded-lg">
                                    <p class="mt-4 text-gray-700">{!! $noticia->contenido !!}</p>
                                    @if($noticia->multimedia)
                                        <p class="mt-2"><strong>Multimedia:</strong> <a href="{{ $noticia->multimedia }}" target="_blank" class="text-blue-600 hover:underline">Ver Multimedia</a></p>
                                    @endif
                                </div>
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

    <script>
        function openModal(id) {
            document.getElementById('modal-' + id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).classList.add('hidden');
        }
    </script>

</x-app-layout>
