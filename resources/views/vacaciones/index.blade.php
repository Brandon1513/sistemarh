<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Solicitudes de Vacaciones') }}
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
                    <div class="mb-4">
                        <a href="{{ route('vacaciones.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                            {{ __('Solicitar Vacaciones') }}
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
                            <thead>
                                <tr class="text-sm leading-normal text-gray-600 uppercase bg-gray-200">
                                    <th class="px-6 py-3 text-left">Empleado</th>
                                    <th class="px-6 py-3 text-left">Periodo Correspondiente</th>
                                    <th class="px-6 py-3 text-left">Fecha de Inicio</th>
                                    <th class="px-6 py-3 text-left">Fecha de Fin</th>
                                    <th class="px-6 py-3 text-center">Días Solicitados</th>
                                    <th class="px-6 py-3 text-center">Estado</th>
                                    <th class="px-6 py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm font-light text-gray-600">
                                @foreach ($solicitudes as $solicitud)
                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="px-6 py-3 text-left whitespace-nowrap">
                                            {{ $solicitud->empleado->name }}
                                        </td>
                                        <td class="px-6 py-3 text-left">
                                            {{ $solicitud->periodo_correspondiente }}
                                        </td>
                                        <td class="px-6 py-3 text-left">
                                            {{ \Carbon\Carbon::parse($solicitud->fecha_inicio)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-3 text-left">
                                            {{ \Carbon\Carbon::parse($solicitud->fecha_fin)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            {{ $solicitud->dias_solicitados }}
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="py-1 px-3 rounded-full text-xs {{ $solicitud->estado == 'pendiente' ? 'bg-yellow-200 text-yellow-600' : ($solicitud->estado == 'aprobado' ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600') }}">
                                                {{ ucfirst($solicitud->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            <div class="flex justify-center space-x-2 item-center">
                                                <a href="{{ route('vacaciones.show', $solicitud->id) }}" class="text-blue-500 hover:underline">
                                                    Ver
                                                </a>
                                                
                                                <!-- Check if the authenticated user is the creator of the request -->
                                                @if ($solicitud->empleado_id == auth()->id())
                                                    <span class="text-gray-500">No autorizado</span>
                                                @elseif ($solicitud->estado == 'pendiente')
                                                    <a href="{{ route('vacaciones.aprobar', $solicitud->id) }}" class="text-green-500 hover:underline">
                                                        Aprobar
                                                    </a>
                                                    <a href="{{ route('vacaciones.rechazar', $solicitud->id) }}" class="text-red-500 hover:underline">
                                                        Rechazar
                                                    </a>
                                                @endif
                                            </div>
                                        </td>                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $solicitudes->links() }} <!-- Agregar paginación -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
