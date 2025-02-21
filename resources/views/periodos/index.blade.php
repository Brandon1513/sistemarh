<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Historial de Períodos de Vacaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Contenedor para el campo de búsqueda y botón de crear periodo -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex-1">
                            <form method="GET" action="{{ route('periodos.index') }}">
                                <input type="text" name="search" placeholder="Buscar por nombre o clave del empleado..." 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring focus:ring-indigo-200 focus:border-indigo-300 sm:text-sm">
                            </form>
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('periodos.create') }}" 
                               class="px-5 py-2 font-semibold text-white transition-colors duration-200 bg-blue-600 rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                {{ __('Crear Nuevo Periodo') }}
                            </a>
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('periodos.export') }}" 
                               class="px-5 py-2 font-semibold text-white transition-colors duration-200 bg-green-600 rounded-lg shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                Descargar Excel
                            </a>
                        </div>
                        
                    </div>

                    <!-- Tabla de resultados -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Nombre del Empleado</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Año del Periodo</th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periodos as $periodo)
                                    <tr>
                                        <td class="px-4 py-4 text-sm text-gray-700 border-b">{{ $periodo->empleado->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-700 border-b">{{ $periodo->anio }}</td>
                                        <td class="px-4 py-4 text-sm text-blue-600 border-b">
                                            <a href="{{ route('periodos.edit', $periodo->id) }}" class="hover:underline">Editar</a>
                                            <form action="{{ route('periodos_vacaciones.toggle-activo', $periodo->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1 text-white rounded {{ $periodo->activo ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                                                    {{ $periodo->activo ? 'Inactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        </td>
                                       
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-sm text-center text-gray-500">
                                            No se encontraron resultados para tu búsqueda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $periodos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
