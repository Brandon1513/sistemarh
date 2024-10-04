<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Gestionar Recursos Humanos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Botón Responsivo -->
                    <a href="{{ route('recursoshumanos.create') }}" class="inline-flex items-center justify-center block w-full px-4 py-2 mb-4 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-600 border border-transparent rounded-md sm:w-auto hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                        {{ __('Agregar Recursos Humanos') }}
                    </a>

                    <!-- Tabla Responsiva -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        NOMBRE
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        EMAIL
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        ACCIONES
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recursosHumanos as $recursoshumano)
                                    <tr>
                                        <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                            {{ $recursoshumano->name }}
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                            {{ $recursoshumano->email }}
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                            <!-- Acciones Responsivas -->
                                            <div class="flex flex-col sm:flex-row">
                                                <a href="{{ route('recursoshumanos.edit', $recursoshumano->id) }}" class="block w-full px-4 py-2 mb-2 text-xs font-semibold text-center text-white uppercase transition bg-yellow-600 border border-transparent rounded-md sm:w-auto sm:mb-0 sm:mr-2 hover:bg-yellow-700 active:bg-yellow-800 focus:outline-none focus:border-yellow-800 focus:ring focus:ring-yellow-200">
                                                    {{ __('Editar') }}
                                                </a>
                                                <form action="{{ route('recursoshumanos.destroy', $recursoshumano->id) }}" method="POST" class="block w-full sm:w-auto">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="block w-full px-4 py-2 text-xs font-semibold text-center text-white uppercase transition bg-red-600 border border-transparent rounded-md sm:w-auto hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring focus:ring-red-200">
                                                        {{ __('Eliminar') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- Fin del contenedor de tabla responsiva -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
