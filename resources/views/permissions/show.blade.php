<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detalle del Permiso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <strong>Nombre del Empleado:</strong> {{ $permission->user->name ?? 'Sin asignar' }}
                    </div>
                    <div class="mb-4">
                        <strong>Departamento:</strong> {{ $permission->department->name }}
                    </div>
                    <div class="mb-4">
                        <strong>Tipo de Permiso:</strong> {{ $permission->entry_exit_type  }}
                    </div>
                    <div class="mb-4">
                        <strong>Horario Oficial:</strong> {{ $permission->official_schedule }}
                    </div>
                    <div class="mb-4">
                        <strong>Hora de Entrada o Salida:</strong> {{ $permission->entry_exit_time }}
                    </div>
                    <div class="mb-4">
                        <strong>Fecha del Permiso:</strong> {{ $permission->date }}
                    </div>
                    <div class="mb-4">
                        <strong>Motivo:</strong> {{ $permission->reason }}
                    </div>
                    <div class="mb-4">
                        <strong>Estado:</strong>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($permission->status == 'pendiente') bg-yellow-100 text-yellow-800 
                        @elseif($permission->status == 'aprobado') bg-green-100 text-green-800 
                        @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($permission->status) }}
                        </span>
                    </div>
                    <div class="mb-4">
                        <strong>Documento de Soporte:</strong>
                        @if($permission->supporting_document)
                            @if(pathinfo($permission->supporting_document, PATHINFO_EXTENSION) == 'pdf')
                                <a href="{{ asset('storage/' . $permission->supporting_document) }}" target="_blank" class="text-blue-500">Ver PDF</a>
                            @else
                                <img src="{{ asset('storage/' . $permission->supporting_document) }}" alt="Documento de Soporte" class="h-auto max-w-full mt-2 rounded-md">
                            @endif
                        @else
                            <p>No hay documento adjunto.</p>
                        @endif
                    </div>
                    
                
                    <a href="{{ route('permissions.index') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring focus:ring-gray-200 disabled:opacity-25">Volver</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
