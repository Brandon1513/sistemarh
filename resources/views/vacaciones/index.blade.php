<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Mis Solicitudes de Vacaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Botón para crear un nuevo permiso -->
                    <div class="mb-4">
                        <a href="{{ route('vacaciones.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                            {{ __('Solicitar Vacaciones') }}
                        </a>
                    </div>
                    @if($solicitudes->isEmpty())
                        <p class="text-gray-600">No tienes solicitudes de vacaciones registradas.</p>
                    @else
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Periodo Correspondiente</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Fecha de Inicio</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Fecha de Fin</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Días Solicitados</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
                                    <tr>
                                        <td class="px-6 py-4 border-b border-gray-200">{{ $solicitud->periodo_correspondiente }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200">{{ $solicitud->fecha_inicio }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200">{{ $solicitud->fecha_fin }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200">{{ $solicitud->dias_solicitados }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($solicitud->estado == 'pendiente') bg-yellow-100 text-yellow-800 
                                            @elseif($solicitud->estado == 'aprobado') bg-green-100 text-green-800 
                                            @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($solicitud->estado) }}
                                            </span>
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
