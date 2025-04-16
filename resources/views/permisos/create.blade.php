<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Solicitar Nuevo Permiso') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('permisos.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Campos existentes... -->
                        {{-- Departamento --}}
                        <div class="mb-4">
                            <label for="departamento_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Departamento') }}
                            </label>
                            <select name="departamento_id" id="departamento_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" required>
                                @foreach($departamentos as $departamento)
                                    <option value="{{ $departamento->id }}">{{ $departamento->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Fecha de Inicio, Término, Regreso a Laborar --}}
                        <div class="mb-4">
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">{{ __('Fecha de Inicio') }}</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="fecha_termino" class="block text-sm font-medium text-gray-700">{{ __('Fecha de Término') }}</label>
                            <input type="date" name="fecha_termino" id="fecha_termino" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="fecha_regreso_laborar" class="block text-sm font-medium text-gray-700">{{ __('Fecha de Regreso a Laborar') }}</label>
                            <input type="date" name="fecha_regreso_laborar" id="fecha_regreso_laborar" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm">
                        </div>

                        {{-- Tipo y Tipo de Permiso --}}
                        <div class="mb-4">
                            <label for="tipo" class="block text-sm font-medium text-gray-700">{{ __('Tipo de Solicitud') }}</label>
                            <select name="tipo" id="tipo" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" required>
                                <option value="Permiso">Permiso</option>
                                <option value="Comisión">Comisión</option>
                                <option value="Suspensión">Suspensión</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="tipo_permiso" class="block text-sm font-medium text-gray-700">{{ __('Tipo de Permiso') }}</label>
                            <select name="tipo_permiso" id="tipo_permiso" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" required>
                                <option value="Con Goce de Sueldo">Con Goce de Sueldo</option>
                                <option value="Sin Goce de Sueldo">Sin Goce de Sueldo</option>
                            </select>
                        </div>

                        {{-- Día de Descanso --}}
                        <div class="mb-4">
                            <label for="dia_descanso" class="block text-sm font-medium text-gray-700">{{ __('Día de Descanso') }}</label>
                            <select name="dia_descanso" id="dia_descanso" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm">
                                <option value="Lunes">Lunes</option>
                                <option value="Martes">Martes</option>
                                <option value="Miercoles">Miércoles</option>
                                <option value="Jueves">Jueves</option>
                                <option value="Viernes">Viernes</option>
                                <option value="Sabado">Sábado</option>
                                <option value="Domingo">Domingo</option>
                            </select>
                        </div>

                        {{-- Motivo --}}
                        <div class="mb-4">
                            <label for="motivo" class="block text-sm font-medium text-gray-700">{{ __('Observaciones') }}</label>
                            <textarea name="motivo" id="motivo" rows="4" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" required></textarea>
                        </div>

                        {{-- Archivo Adjunto --}}
                        <div class="mb-4">
                            <label for="archivo" class="block text-sm font-medium text-gray-700">
                                {{ __('Archivo Adjunto') }}
                            </label>
                            <input type="file" name="archivo" id="archivo" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm">
                            <p class="mt-2 text-sm text-gray-500">Puedes subir una imagen o PDF.</p>
                        </div>

                        {{-- Vista previa --}}
                        <div id="preview" class="mt-4"></div>
                        <br>
                        {{-- Botón --}}
                        <div class="mb-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none">
                                {{ __('Enviar Solicitud') }}
                            </button>
                        </div>
                    </form>

                    {{-- Errores --}}
                    @if ($errors->any())
                        <div class="p-4 mt-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded-md">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Script para previsualización --}}
    <script>
        document.getElementById('archivo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            preview.innerHTML = '';

            if (file) {
                const fileType = file.type;

                if (fileType.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.classList.add('mt-2', 'max-w-full', 'h-auto', 'rounded-md', 'shadow-md', 'border', 'border-gray-300');
                    preview.appendChild(img);
                } else if (fileType === 'application/pdf') {
                    const iframe = document.createElement('iframe');
                    iframe.src = URL.createObjectURL(file);
                    iframe.classList.add('mt-2', 'w-full', 'h-96', 'rounded-md', 'shadow-md', 'border', 'border-gray-300');
                    preview.appendChild(iframe);
                } else {
                    preview.textContent = 'Archivo seleccionado no soportado para previsualización.';
                }
            }
        });
    </script>
</x-app-layout>
