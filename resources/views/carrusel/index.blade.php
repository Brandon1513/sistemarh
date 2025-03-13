<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Administrar Carrusel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Mensaje de éxito -->
            @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif
            <div class="p-6 overflow-hidden bg-white shadow-xl sm:rounded-lg">
                <a href="{{ route('carrusel.create') }}" class="px-4 py-2 text-white bg-blue-500 rounded">Subir Nueva Imagen</a>

                <div class="grid grid-cols-3 gap-4 mt-6">
                    @foreach ($carruselImages as $image)
                        <div class="relative">
                            <img src="{{ asset('storage/' . $image->image) }}" class="object-cover w-full h-48 rounded-lg">
                            
                            <!-- Botón para abrir el modal de edición -->
                            <button onclick="openEditModal(
                                {{ $image->id }}, 
                                '{{ $image->titulo }}', 
                                '{{ $image->descripcion }}', 
                                '{{ Storage::url($image->image) }}'
                            )"
                            class="absolute px-2 py-1 text-white rounded-full top-2 right-12">
                                ✏️
                            </button>


                            <!-- Botón para eliminar -->
                            <button onclick="deleteImage({{ $image->id }})"
                                    class="absolute px-2 py-1 text-white rounded-full top-2 right-2">
                                ❌
                            </button>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
        <div id="editModal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
            <div class="p-6 bg-white rounded-lg shadow-lg w-96">
                <h2 class="mb-4 text-xl font-bold">Editar Imagen</h2>
                
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
        
                    <input type="hidden" id="editImageId" name="id">
        
                    <!-- Vista previa de la imagen actual -->
                    <img id="editImagePreview" class="object-cover w-full h-48 mb-4 rounded-md">
        
                    <!-- Campo para subir una nueva imagen -->
                    <label for="editImage" class="block font-semibold text-gray-700">Cambiar Imagen</label>
                    <input type="file" id="editImage" name="image" accept="image/*"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    
                    <!-- Campo de Título -->
                    <label for="editTitulo" class="block mt-4 font-semibold text-gray-700">Título</label>
                    <input type="text" id="editTitulo" name="titulo"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    
                    <!-- Campo de Descripción -->
                    <label for="editDescripcion" class="block mt-4 font-semibold text-gray-700">Descripción</label>
                    <textarea id="editDescripcion" name="descripcion"
                              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    
                    <!-- Botones -->
                    <div class="flex justify-end mt-4">
                        <button type="button" class="px-4 py-2 mr-2 text-white bg-gray-400 rounded-md"
                                onclick="closeEditModal()">Cancelar</button>
                        <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-md">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
        
        
    </div>
    <script>
        function openEditModal(id, titulo, descripcion, imageUrl) {
            document.getElementById('editImageId').value = id;
            document.getElementById('editTitulo').value = titulo;
            document.getElementById('editDescripcion').value = descripcion;
            document.getElementById('editImagePreview').src = imageUrl; // Cargar la imagen actual
            document.getElementById('editForm').action = `/carrusel/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        }
    
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
