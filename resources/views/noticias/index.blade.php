<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestión de Noticias') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                
                <!-- Mensaje de éxito -->
                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Barra de búsqueda -->
                    <form method="GET" action="{{ route('noticias.index') }}" class="mb-4">
                        <input type="text" name="search" placeholder="Buscar noticia..." value="{{ request('search') }}" 
                               class="block w-full px-4 py-2 text-sm border-gray-300 rounded-md shadow-sm">
                    </form>

                    <!-- Botón para agregar una nueva noticia -->
                    <a href="{{ route('noticias.create') }}" class="inline-flex items-center px-4 py-2 mb-4 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                        {{ __('Agregar Noticia') }}
                    </a>
                    
                    <!-- Contenedor para hacer la tabla desplazable horizontalmente en pantallas pequeñas -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Título</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Autor</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Fecha</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Estado</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($noticias as $noticia)
                                    <tr>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">{{ $noticia->titulo }}</td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">{{ $noticia->autor }}</td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">{{ $noticia->fecha }}</td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">
                                            {{ $noticia->activo ? 'Activo' : 'Inactivo' }}
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">
                                            <!-- Botón para editar -->
                                            <a href="{{ route('noticias.edit', $noticia->id) }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-yellow-500 border border-transparent rounded-md hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-yellow-200 disabled:opacity-25">
                                                Editar
                                            </a>

                                            <!-- Botón para activar/inactivar -->
                                            <form action="{{ route('noticias.toggle', $noticia->id) }}" method="POST" style="display:inline;" onsubmit="return confirmToggle('{{ $noticia->activo ? 'inactivar' : 'activar' }}', '{{ $noticia->titulo }}');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition {{ $noticia->activo ? 'bg-gray-600 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }} border border-transparent rounded-md active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring focus:ring-gray-200 disabled:opacity-25">
                                                    {{ $noticia->activo ? 'Inactivar' : 'Activar' }}
                                                </button>
                                            </form>

                                            <!-- Botón para eliminar -->
                                            <form action="{{ route('noticias.destroy', $noticia->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta noticia?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-red-600 border border-transparent rounded-md hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring focus:ring-red-200 disabled:opacity-25">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $noticias->links() }} <!-- Paginación -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function confirmToggle(action, noticiaTitulo) {
        return confirm(`¿Estás seguro de que deseas ${action} la noticia "${noticiaTitulo}"?`);
    }
</script>
