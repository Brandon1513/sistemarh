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
                    <form method="POST" action="{{ route('permisos.store') }}">
                        @csrf

                        <!-- Selección del Departamento -->
                        <div class="mb-4">
                            <label for="departamento_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Departamento') }}
                            </label>
                            <select name="departamento_id" id="departamento_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                @foreach($departamentos as $departamento)
                                    <option value="{{ $departamento->id }}">{{ $departamento->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fecha de Inicio -->
                        <div class="mb-4">
                            <label for="fecha_inicio" class="block text-sm font-medium text-gray-700">
                                {{ __('Fecha de Inicio') }}
                            </label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Fecha de Término -->
                        <div class="mb-4">
                            <label for="fecha_termino" class="block text-sm font-medium text-gray-700">
                                {{ __('Fecha de Término') }}
                            </label>
                            <input type="date" name="fecha_termino" id="fecha_termino" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Fecha de Regreso a Laborar -->
                        <div class="mb-4">
                            <label for="fecha_regreso_laborar" class="block text-sm font-medium text-gray-700">
                                {{ __('Fecha de Regreso a Laborar') }}
                            </label>
                            <input type="date" name="fecha_regreso_laborar" id="fecha_regreso_laborar" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <!-- Tipo de Solicitud (Permiso, Comisión, Suspensión) -->
                        <div class="mb-4">
                            <label for="tipo" class="block text-sm font-medium text-gray-700">
                                {{ __('Tipo de Solicitud') }}
                            </label>
                            <select name="tipo" id="tipo" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="Permiso">Permiso</option>
                                <option value="Comisión">Comisión</option>
                                <option value="Suspensión">Suspensión</option>
                            </select>
                        </div>
                        <!-- Tipo de Permiso -->
                        <div class="mb-4">
                            <label for="tipo_permiso" class="block text-sm font-medium text-gray-700">
                                {{ __('Tipo de Permiso') }}
                            </label>
                            <select name="tipo_permiso" id="tipo_permiso" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="Con Goce de Sueldo">{{ __('Con Goce de Sueldo') }}</option>
                                <option value="Sin Goce de Sueldo">{{ __('Sin Goce de Sueldo') }}</option>
                            </select>
                        </div>

                        <!-- Día de Descanso -->
                        <div class="mb-4">
                            <label for="dia_descanso" class="block text-sm font-medium text-gray-700">
                                {{ __('Día de Descanso') }}
                            </label>
                            <select name="dia_descanso" id="dia_descanso" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="Lunes">Lunes</option>
                                <option value="Martes">Martes</option>
                                <option value="Miercoles">Miercoles</option>
                                <option value="Jueves">Jueves</option>
                                <option value="Viernes">Viernes</option>
                                <option value="Sabado">Sábado</option>
                                <option value="Domingo">Domingo</option>
                                <!-- Puedes agregar más opciones aquí -->
                            </select>
                        </div>
                        <!-- Campo Motivo -->
                        <div class="mb-4">
                            <label for="motivo" class="block text-sm font-medium text-gray-700">
                                {{ __('Observaciones') }}
                            </label>
                            <textarea name="motivo" id="motivo" rows="4" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required></textarea>
                        </div>

                        <!-- Botón para Enviar -->
                        <div class="mb-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                                {{ __('Enviar Solicitud') }}
                            </button>
                        </div>

                    </form>
                    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
