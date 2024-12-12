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
                    <h3>Información del Empleado</h3>
                    <p><strong>Nombre:</strong> {{ $vacation->empleado->name }}</p>
                    <p><strong>Departamento:</strong> {{ $vacation->departamento->name }}</p>
                    
                    <h3>Detalles de la Solicitud</h3>
                    <p><strong>Fecha de Inicio:</strong> {{ $vacation->fecha_inicio }}</p>
                    <p><strong>Fecha de Fin:</strong> {{ $vacation->fecha_fin }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($vacation->estado) }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
