<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Detalles del Permiso') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Detalles del Permiso</h3>

                    <div class="mt-4">
                        <p><strong>Empleado:</strong> {{ $solicitud->empleado->name }}</p>
                        <p><strong>Departamento:</strong> {{ $solicitud->departamento->name }}</p>
                        <p><strong>Fecha de Creación:</strong> {{ $solicitud->created_at->format('d-m-Y') }}</p>
                        <p><strong>Fecha de Inicio:</strong> {{ $solicitud->fecha_inicio }}</p>
                        <p><strong>Fecha de Término:</strong> {{ $solicitud->fecha_termino }}</p>
                        <p><strong>Fecha de Regreso a Laborar:</strong> {{ $solicitud->fecha_regreso_laborar ?? 'No especificada' }}</p>
                        <p><strong>Tipo de Solicitud:</strong> {{ $solicitud->tipo }}</p>
                        <p><strong>Día de Descanso:</strong> {{ $solicitud->dia_descanso ?? 'No especificado' }}</p>
                        <p><strong>Motivo:</strong> {{ $solicitud->motivo }}</p>
                        <p><strong>Estado:</strong> {{ ucfirst($solicitud->estado) }}</p>
                    </div>

                    <!-- 📎 Mostrar Archivo Adjunto si existe -->
                    @if($solicitud->archivo)
                        <div class="mt-6">
                            <p class="mb-2 text-sm font-medium text-gray-700">Archivo Adjunto:</p>
                            @php
                                $extension = pathinfo($solicitud->archivo, PATHINFO_EXTENSION);
                                $archivoUrl = Storage::url($solicitud->archivo);
                            @endphp

                            <div class="mb-4">
                                @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $archivoUrl }}" alt="Archivo adjunto" class="h-auto max-w-full border border-gray-300 rounded shadow-md">
                                @elseif($extension === 'pdf')
                                    <iframe src="{{ $archivoUrl }}" class="w-full border border-gray-300 rounded shadow-md h-96"></iframe>
                                @else
                                    <p class="text-sm text-gray-600">Archivo disponible para descarga:</p>
                                @endif
                            </div>

                            <!-- 🔽 Botón de descarga -->
                            <a href="{{ $archivoUrl }}" download
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white transition bg-purple-800 rounded-md hover:bg-purple-900">
                                Descargar Archivo
                            </a>
                        </div>
                    @endif


                    <!-- Botones para jefe -->
                    @if(auth()->user()->hasRole('jefe') && $solicitud->estado == 'pendiente' && $solicitud->empleado_id !== auth()->id())
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

                    <!-- Botón Volver -->
                    <div class="mt-6">
                        <a href="{{ route('permisos.index') }}" class="text-blue-600 hover:text-blue-900">Volver a la lista</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
