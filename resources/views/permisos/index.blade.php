<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Permisos de Ausencia de Personal') }}
        </h2>
    </x-slot>

    <div class="py-12"  style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Botón para crear un nuevo permiso -->
                    <div class="mb-4">
                        <a href="{{ route('permisos.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                            {{ __('Solicitar Nuevo Permiso') }}
                        </a>
                    </div>

                    <!-- Contenedor para la tabla (para hacerla desplazable horizontalmente en pantallas pequeñas) -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
                            <thead class="bg-gray-50">
                                <tr class="text-sm leading-normal text-gray-600 uppercase bg-gray-200">
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        Nombre del Empleado
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        Departamento
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        Fecha del Permiso
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        Motivo
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        Estado
                                    </th>
                                    <th class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
                                    <tr>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">
                                            {{ $solicitud->empleado->name }}
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">
                                            {{ $solicitud->departamento->name }}
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($solicitud->fecha_termino)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">
                                            {{ $solicitud->motivo }}
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($solicitud->estado == 'pendiente') bg-yellow-100 text-yellow-800 
                                            @elseif($solicitud->estado == 'aprobado') bg-green-100 text-green-800 
                                            @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($solicitud->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 border-b border-gray-200 whitespace-nowrap">
                                            <!-- Botón Ver siempre disponible -->
                                            <a href="{{ route('permisos.show', $solicitud->id) }}" class="text-blue-600 hover:text-blue-900">Ver</a>

                                            <!-- Botones Aprobar y Rechazar, solo visibles para el jefe y si el estado es pendiente -->
                                            @if(auth()->user()->hasRole('jefe') && $solicitud->estado == 'pendiente' && $solicitud->empleado_id !== auth()->id())
                                                <form action="{{ route('permisos.aprobar', $solicitud->id) }}" method="POST" class="inline-block ml-2">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Aprobar</button>
                                                </form>
                                                <form action="{{ route('permisos.rechazar', $solicitud->id) }}" method="POST" class="inline-block ml-2">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Rechazar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Enlaces de paginación -->
                    <div class="mt-4">
                        {{ $solicitudes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
