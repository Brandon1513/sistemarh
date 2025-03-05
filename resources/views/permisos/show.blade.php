<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detalles del Permiso') }}
        </h2>
    </x-slot>

    <div class="py-12"  style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Detalles del Permiso</h3>

                    <div class="mt-4">
                        <p><strong>Empleado:</strong> {{ $solicitud->empleado->name }}</p>
                        <p><strong>Departamento:</strong> {{ $solicitud->departamento->name }}</p>
                        <!-- Fecha de Creación -->
                        <p><strong>Fecha de Creación:</strong> {{ $solicitud->created_at->format('d-m-Y') }}</p>
                        <p><strong>Fecha de Inicio:</strong> {{ $solicitud->fecha_inicio}}</p>
                        <p><strong>Fecha de Término:</strong> {{ $solicitud->fecha_termino}}</p>

                        <!-- Fecha de Regreso a Laborar -->
                        <p><strong>Fecha de Regreso a Laborar:</strong> {{ $solicitud->fecha_regreso_laborar ?? 'No especificada' }}</p>

                        <!-- Tipo de Solicitud -->
                        <p><strong>Tipo de Solicitud:</strong> {{ $solicitud->tipo }}</p>

                        <!-- Día de Descanso -->
                        <p><strong>Día de Descanso:</strong> {{ $solicitud->dia_descanso ?? 'No especificado' }}</p>

                        <p><strong>Motivo:</strong> {{ $solicitud->motivo }}</p>
                        <p><strong>Estado:</strong> {{ ucfirst($solicitud->estado) }}</p>
                    </div>

                    <!-- Mostrar los botones de aprobar o rechazar solo si el usuario es el jefe -->
                    @if(auth()->user()->hasRole('jefe') && $solicitud->estado == 'pendiente')
                        <div class="mt-4">
                            <form action="{{ route('permisos.aprobar', $solicitud->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">Aprobar</button>
                            </form>

                            <form action="{{ route('permisos.rechazar', $solicitud->id) }}" method="POST" class="inline-block ml-2">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900">Rechazar</button>
                            </form>
                        </div>
                    @endif

                    <!-- Botón de volver -->
                    <div class="mt-6">
                        <a href="{{ route('permisos.index') }}" class="text-blue-600 hover:text-blue-900">Volver a la lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
