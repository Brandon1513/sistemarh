<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Solicitar Vacaciones') }}
        </h2>
    </x-slot>

    <div class="py-12"  style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('vacaciones.store') }}">
                        @csrf

                        <!-- Nombre -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                {{ __('Nombre del Empleado') }}
                            </label>
                            <input type="text" name="name" id="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ $user->name }}" readonly>
                        </div>

                        <!-- Clave del Empleado -->
                        <div class="mb-4">
                            <label for="clave_empleado" class="block text-sm font-medium text-gray-700">
                                {{ __('Clave del Empleado') }}
                            </label>
                            <input type="text" name="clave_empleado" id="clave_empleado" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ $user->clave_empleado }}" readonly>
                        </div>

                       <!-- Fecha de Ingreso -->
                        <div class="mb-4">
                            <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700">
                                {{ __('Fecha de Ingreso') }}
                            </label>
                            <input type="date" name="fecha_ingreso" id="fecha_ingreso" 
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                value="{{ $user->fecha_ingreso ? \Carbon\Carbon::parse($user->fecha_ingreso)->format('Y-m-d') : '' }}" readonly>
                        </div>

                        <!-- Departamento -->
                        <div class="mb-4">
                            <label for="departamento" class="block text-sm font-medium text-gray-700">
                                {{ __('Departamento') }}
                            </label>
                            <input type="text" name="departamento" id="departamento" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" value="{{ $departamentoNombre }}" readonly>

                        </div>

                        <!-- Periodo correspondiente -->
                        <div class="mb-4">
                            <label for="periodo_correspondiente" class="block text-sm font-medium text-gray-700">
                                {{ __('Periodo correspondiente') }}
                            </label>
                            <select id="periodo_correspondiente" name="periodo_correspondiente" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Selecciona un periodo</option>
                                @foreach($periodos as $periodo)
                                    <option value="{{ $periodo->anio }}" data-dias-disponibles="{{ $periodo->dias_disponibles }}">
                                        {{ $periodo->anio }} - Días disponibles: {{ $periodo->dias_disponibles }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Días que Correspondan -->
                        <div class="mb-4">
                            <label for="dias_corresponden" class="block text-sm font-medium text-gray-700">
                                {{ __('Días que Correspondan') }}
                            </label>
                            <input type="number" name="dias_corresponden" id="dias_corresponden" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"  readonly>
                        </div>
                        

                        <!-- Días Solicitados -->
                        <div class="mb-4">
                            <label for="dias_solicitados" class="block text-sm font-medium text-gray-700">
                                {{ __('Días Solicitados') }}
                            </label>
                            <input type="number" name="dias_solicitados" id="dias_solicitados" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"  required>
                        </div>

                        <!-- Pendientes por Disfrutar -->
                        <div class="mb-4">
                            <label for="pendientes_disfrutar" class="block text-sm font-medium text-gray-700">
                                {{ __('Pendientes por Disfrutar') }}
                            </label>
                            <input type="number" name="pendientes_disfrutar" id="pendientes_disfrutar" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"  readonly>
                        </div>
                        <!-- Fecha de Inicio de Vacaciones -->
                        <div class="mb-4">
                            <label for="fecha_inicio_vacaciones" class="block text-sm font-medium text-gray-700">
                                {{ __('Fecha de Inicio de Vacaciones') }}
                            </label>
                            <input type="date" name="fecha_inicio_vacaciones" id="fecha_inicio_vacaciones" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Fecha de Término de Vacaciones -->
                        <div class="mb-4">
                            <label for="fecha_termino_vacaciones" class="block text-sm font-medium text-gray-700">
                                {{ __('Fecha de Término de Vacaciones') }}
                            </label>
                            <input type="date" name="fecha_termino_vacaciones" id="fecha_termino_vacaciones" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <!-- Fecha para Presentarse a Trabajar -->
                        <div class="mb-4">
                            <label for="fecha_presentarse_trabajar" class="block text-sm font-medium text-gray-700">
                                {{ __('Fecha para Presentarse a Trabajar') }}
                            </label>
                            <input type="date" name="fecha_presentarse_trabajar" id="fecha_presentarse_trabajar" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        </div>

                        <input type="hidden" name="estado" value="pendiente">

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
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- Modal de advertencia -->
<div id="vacationModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
    <div class="max-w-lg p-8 bg-white rounded-lg shadow-lg">
        <h2 class="mb-4 text-lg font-semibold">Advertencia de Solicitud de Vacaciones</h2>
        <p>Las solicitudes de vacaciones deben realizarse con un mínimo de 7 días de anticipación. Por favor, confirme que está de acuerdo para proceder con la solicitud.</p>
        
        <!-- Checkbox de aceptación -->
        <div class="mt-4">
            <input type="checkbox" id="acceptTerms" class="mr-2">
            <label for="acceptTerms">Acepto los términos y condiciones</label>
        </div>
        
        <!-- Botón para proceder -->
        <div class="mt-6 text-right">
            <button id="proceedButton" class="px-4 py-2 text-white bg-blue-500 rounded disabled:opacity-50" disabled>
                Proceder
            </button>
        </div>
    </div>
</div>

<!-- JavaScript para controlar el modal -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mostrar el modal al cargar la página
        const modal = document.getElementById('vacationModal');
        modal.classList.remove('hidden');
        
        // Habilitar el botón "Proceder" cuando se marca el checkbox
        const acceptCheckbox = document.getElementById('acceptTerms');
        const proceedButton = document.getElementById('proceedButton');
        
        acceptCheckbox.addEventListener('change', function () {
            proceedButton.disabled = !this.checked;
        });
        
        // Ocultar el modal cuando se hace clic en "Proceder"
        proceedButton.addEventListener('click', function () {
            modal.classList.add('hidden');
        });
    });
</script>



</x-app-layout>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const periodoSelect = document.getElementById('periodo_correspondiente');
        const diasCorrespondenInput = document.getElementById('dias_corresponden');
        const diasSolicitadosInput = document.getElementById('dias_solicitados');
        const pendientesDisfrutarInput = document.getElementById('pendientes_disfrutar');

        // Actualiza "Días que Correspondan" cuando cambia el periodo
        periodoSelect.addEventListener('change', function () {
            const diasDisponibles = periodoSelect.options[periodoSelect.selectedIndex].getAttribute('data-dias-disponibles');
            diasCorrespondenInput.value = diasDisponibles;
            actualizarPendientes();
        });

        // Actualiza "Pendientes por Disfrutar" cuando cambia "Días Solicitados"
        diasSolicitadosInput.addEventListener('input', function () {
            actualizarPendientes();
        });

        // Función para calcular los días pendientes por disfrutar
        function actualizarPendientes() {
            const diasCorresponden = parseInt(diasCorrespondenInput.value) || 0;
            const diasSolicitados = parseInt(diasSolicitadosInput.value) || 0;
            const pendientes = diasCorresponden - diasSolicitados;
            pendientesDisfrutarInput.value = pendientes;
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const diasFestivos = [
        '2025-01-01', '2025-02-03', '2025-03-17', '2025-04-18', '2025-04-19',
        '2025-05-01', '2025-09-16','2025-10-12', '2025-11-17','2025-12-24', '2025-12-25','2025-12-31'
    ];

    flatpickr("#fecha_inicio_vacaciones", {
        dateFormat: "Y-m-d",
        disable: [
            function (date) {
                // Deshabilitar domingos
                if (date.getDay() === 0) {
                    return true;
                }
                // Deshabilitar días festivos
                return diasFestivos.includes(date.toISOString().split('T')[0]);
            }
        ],
        locale: {
            firstDayOfWeek: 1 // Comenzar la semana en lunes
        }
    });

    flatpickr("#fecha_termino_vacaciones", {
        dateFormat: "Y-m-d",
        disable: [
            function (date) {
                return date.getDay() === 0 || diasFestivos.includes(date.toISOString().split('T')[0]);
            }
        ]
    });

    flatpickr("#fecha_presentarse_trabajar", {
        dateFormat: "Y-m-d",
        disable: [
            function (date) {
                return date.getDay() === 0 || diasFestivos.includes(date.toISOString().split('T')[0]);
            }
        ]
    });
});


</script>
