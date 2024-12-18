<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detalle de Solicitud de Vacaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="mb-4 text-lg font-semibold">Información de la Solicitud</h3>
                    <p><strong>Empleado:</strong> {{ $vacation->empleado->name }}</p>
                    <p><strong>Departamento:</strong> {{ $vacation->departamento->name }}</p>
                    <p><strong>Fecha de Solicitud:</strong> {{ $vacation->fecha_solicitud ? \Carbon\Carbon::parse($vacation->fecha_solicitud)->format('d/m/Y') : 'No disponible' }}</p>
                    <p><strong>Periodo Correspondiente:</strong> {{ $vacation->periodo_correspondiente }}</p>
                    <p><strong>Fecha de Inicio:</strong> {{ $vacation->fecha_inicio ? \Carbon\Carbon::parse($vacation->fecha_inicio)->format('d/m/Y') : 'No disponible' }}</p>
                    <p><strong>Fecha de Fin:</strong> {{ $vacation->fecha_fin ? \Carbon\Carbon::parse($vacation->fecha_fin)->format('d/m/Y') : 'No disponible' }}</p>
                    <p><strong>Fecha de Reincorporación:</strong> {{ $vacation->fecha_reincorporacion ? \Carbon\Carbon::parse($vacation->fecha_reincorporacion)->format('d/m/Y') : 'No disponible' }}</p>
                    <p><strong>Días Solicitados:</strong> {{ $vacation->dias_solicitados }}</p>
                    <p><strong>Estado:</strong> 
                        <span class="px-2 py-1 rounded {{ $vacation->estado == 'aprobado' ? 'bg-green-200 text-green-700' : ($vacation->estado == 'rechazado' ? 'bg-red-200 text-red-700' : 'bg-yellow-200 text-yellow-700') }}">
                            {{ ucfirst($vacation->estado) }}
                        </span>
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('solicitudes_vacaciones.index') }}" class="text-blue-600 hover:underline">Regresar a la lista de solicitudes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>