<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Agregar Periodo de Vacaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('periodos.store') }}">
                        @csrf

                        <!-- Seleccionar Múltiples Empleados -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('Selecciona Empleados') }}
                            </label>
                            @foreach($usuarios as $usuario)
                                <div>
                                    <input type="checkbox" name="empleado_id[]" value="{{ $usuario->id }}" class="empleado-checkbox">
                                    <label>{{ $usuario->name }}</label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Año -->
                        <div class="mb-4">
                            <label for="anio" class="block text-sm font-medium text-gray-700">
                                {{ __('Año') }}
                            </label>
                            <input type="number" name="anio" id="anio" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Resultados Dinámicos para Días Correspondientes -->
                        <div class="mb-4" id="resultados-dias">
                            <!-- Aquí se mostrarán los días correspondientes por empleado -->
                        </div>

                        <!-- Botón para Enviar -->
                        <div class="mb-4">
                            <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                {{ __('Guardar Periodo') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const anioInput = document.getElementById('anio');
        const empleadosCheckboxes = document.querySelectorAll('.empleado-checkbox');
        const resultadosDiv = document.getElementById('resultados-dias');
    
        function fetchVacationDays() {
            // Obtiene los empleados seleccionados
            const empleadosSeleccionados = Array.from(empleadosCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);

            if (empleadosSeleccionados.length === 0) {
                resultadosDiv.innerHTML = '<p>Selecciona al menos un empleado.</p>';
                return;
            }

            fetch('{{ route("calculate.days") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    anio: anioInput.value,
                    empleado_ids: empleadosSeleccionados
                })
            })
            .then(response => response.json())
            .then(data => {
                // Limpia resultados previos
                resultadosDiv.innerHTML = '';
                // Itera sobre los datos de los empleados y muestra sus días correspondientes
                data.forEach(item => {
                    const resultado = document.createElement('p');
                    resultado.innerText = `Empleado: ${item.empleado} - Días que corresponden: ${item.dias_corresponden} - Días disponibles: ${item.dias_disponibles}`;
                    resultadosDiv.appendChild(resultado);
                });
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Eventos para actualizar los días automáticamente
        anioInput.addEventListener('change', fetchVacationDays);
        empleadosCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', fetchVacationDays);
        });
    });
</script>
