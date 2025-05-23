<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Solicitudes Control de Ausencia del Personal') }}
        </h2>
    </x-slot>

    <div class="py-12"  style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <!-- Formulario de búsqueda con descarga de ZIP -->
                    <form method="GET" action="{{ route('solicitudes_permisos.index') }}">
                        <div class="flex mb-4 space-x-4">
                            <input type="text" name="search" placeholder="Buscar por nombre" value="{{ request('search') }}" class="w-1/3 px-3 py-2 border rounded-md">
                            <!-- Campo para la fecha de inicio -->
                            <input type="date" name="start_date" placeholder="Fecha de inicio" value="{{ request('start_date') }}" class="w-1/3 px-3 py-2 border rounded-md">
                            <!-- Campo para la fecha de fin -->
                            <input type="date" name="end_date" placeholder="Fecha de fin" value="{{ request('end_date') }}" class="w-1/3 px-3 py-2 border rounded-md">
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                Buscar
                            </button>
                        </div>
                    </form>

                    <!-- Botones de descarga -->
                    <div class="flex justify-between mb-4">
                        <a href="{{ route('solicitudes_permisos.export', ['search' => request('search'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                            Descargar Libro Mayor
                        </a>
                        <a href="{{ route('solicitudes_permisos.exportWeek') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600">
                            Descargar Solicitudes de la Semana
                        </a>

                        <!-- Formulario de descarga ZIP -->
                        <!-- Formulario de descarga ZIP -->
                        <form action="{{ route('solicitudes_permisos.download-zip') }}" method="GET">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600">
                                Descargar Solicitudes en ZIP
                            </button>
                        </form>

                    </div>

                    @if($permisos->isEmpty())
                        <p>No hay solicitudes aprobadas o rechazadas para mostrar.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                        Empleado
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                        Departamento
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                        Fecha del Permiso
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                        Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($permisos as $permiso)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $permiso->empleado->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $permiso->departamento->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $permiso->fecha_inicio }} - {{ $permiso->fecha_termino }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($permiso->estado == 'aprobado') bg-green-100 text-green-800 
                                            @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($permiso->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                            <a href="{{ route('solicitudes_permisos.show', $permiso->id) }}" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                            <a href="{{ route('solicitudes_permisos.download', $permiso->id) }}" class="ml-2 text-green-600 hover:text-green-900">Descargar PDF</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
