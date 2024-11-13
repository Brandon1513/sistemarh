<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Solicitar Permiso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('permissions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="position" class="block text-sm font-medium text-gray-700">Puesto</label>
                            <input type="text" name="position" id="position" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="department_id" class="block text-sm font-medium text-gray-700">Departamento</label>
                            <select name="department_id" id="department_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">Selecciona un departamento</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="entry_exit_type" class="block text-sm font-medium text-gray-700">¿El permiso es para la entrada o salida?</label>
                            <select name="entry_exit_type" id="entry_exit_type" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Selecciona una opción</option>
                                <option value="entrada">Entrada</option>
                                <option value="salida">Salida</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="official_schedule" class="block text-sm font-medium text-gray-700">Horario Oficial:</label>
                            <input type="time" name="official_schedule" id="official_schedule" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="entry_exit_time" class="block text-sm font-medium text-gray-700">Hora de Entrada/Salida:</label>
                            <input type="time" name="entry_exit_time" id="entry_exit_time" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">Fecha del Permiso:</label>
                            <input type="date" name="date" id="date" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required >
                        </div>
                        <div class="mb-4">
                            <label for="reason" class="block text-sm font-medium text-gray-700">Motivo (sé claro y conciso con el motivo de tu permiso):</label>
                            <textarea name="reason" id="reason" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="supporting_document" class="block text-sm font-medium text-gray-700">Documento de Soporte (opcional)</label>
                            <input type="file" name="supporting_document" id="supporting_document" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <p class="mt-2 text-sm text-gray-500">Puedes subir una imagen o un archivo PDF.</p>
                        </div>
                         <!-- Campo de Checkbox de Aviso de Privacidad -->
                         <div class="mb-4">
                            <label for="aviso_privacidad" class="flex items-center">
                                <input id="aviso_privacidad" type="checkbox" name="aviso_privacidad" class="mr-2 border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <span class="text-sm text-gray-600">
                                    He leído y acepto el 
                                    <a href="https://dasavena.com/pages/terminos-condiciones/#aviso-privacidad" class="text-blue-500 underline hover:text-blue-700" target="_blank">Aviso de Privacidad</a>.
                                </span>
                            </label>
                        </div>
                        <div id="preview" class="mt-4"></div> <!-- Aquí se mostrará la previsualización -->
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-green-600 border border-transparent rounded-md hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring focus:ring-green-200 disabled:opacity-25">
                            Solicitar Permiso
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('supporting_document').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            preview.innerHTML = '';

            if (file) {
                const fileType = file.type;

                if (fileType.startsWith('image/')) {
                    // Mostrar imagen
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.classList.add('mt-2', 'max-w-full', 'h-auto', 'rounded-md', 'shadow-md', 'border', 'border-gray-300');
                    preview.appendChild(img);
                } else if (fileType === 'application/pdf') {
                    // Mostrar PDF con ajustes
                    const iframe = document.createElement('iframe');
                    iframe.src = URL.createObjectURL(file);
                    iframe.classList.add('mt-2', 'w-full', 'h-96', 'rounded-md', 'shadow-md', 'border', 'border-gray-300');
                    preview.appendChild(iframe);
                } else {
                    // Tipo de archivo no soportado
                    preview.textContent = 'Archivo seleccionado no soportado para previsualización.';
                }
            }
        });
    </script>

    <script>
        window.onload = function() {
            const dateInput = document.getElementById('date');
            
            // Obtener la fecha actual
            const today = new Date();
            
            // Obtener el día anterior
            const yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);
            const yesterdayISO = yesterday.toISOString().split('T')[0];
            
            // Establecer el mínimo como el día anterior y no establecer límite máximo
            dateInput.min = yesterdayISO;
        };
    </script>

    
</x-app-layout>
