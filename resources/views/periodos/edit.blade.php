<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Periodo de Vacaciones') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('periodos.update', $periodo->id) }}">


                        @csrf
                        @method('PUT')

                        <!-- Nombre del Empleado (Solo lectura) -->
                        <div class="mb-4">
                            <label for="empleado" class="block text-sm font-medium text-gray-700">
                                {{ __('Nombre del Empleado') }}
                            </label>
                            <input type="text" id="empleado" value="{{ $periodo->empleado->name }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" readonly>
                        </div>

                        <!-- Año del Periodo (Solo lectura) -->
                        <div class="mb-4">
                            <label for="anio" class="block text-sm font-medium text-gray-700">
                                {{ __('Año del Periodo') }}
                            </label>
                            <input type="text" id="anio" value="{{ $periodo->anio }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" readonly>
                        </div>

                        <!-- Días Correspondientes (Solo lectura) -->
                        <div class="mb-4">
                            <label for="dias_correspondientes" class="block text-sm font-medium text-gray-700">
                                {{ __('Días que Corresponden') }}
                            </label>
                            <input type="text" id="dias_correspondientes" value="{{ $periodo->dias_corresponden }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" readonly>
                        </div>

                        <!-- Días Disponibles (Editable) -->
                        <div class="mb-4">
                            <label for="dias_disponibles" class="block text-sm font-medium text-gray-700">
                                {{ __('Días Disponibles') }}
                            </label>
                            <input type="number" name="dias_disponibles" id="dias_disponibles" value="{{ $periodo->dias_disponibles }}" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm sm:text-sm" min="0" required>
                        </div>

                        <!-- Botón para guardar cambios -->
                        <div class="mb-4">
                            <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                {{ __('Guardar Cambios') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
