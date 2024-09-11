<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Permisos Solicitados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                     <!-- Botón para crear un nuevo permiso -->
                     <div class="mb-4">
                        <a href="{{ route('permissions.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25">
                            {{ __('Solicitar Nuevo Permiso') }}
                        </a>
                    </div>
                    <!-- Tabla de permisos -->
                    <table class="min-w-full bg-white border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                    Nombre del Empleado
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                    Departamento
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                    Fecha del Permiso
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                    Motivo
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase border-b border-gray-200">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                        {{ $permission->user->name }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                        {{ $permission->department->name }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                        {{ $permission->date }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                        {{ $permission->reason }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($permission->status == 'pendiente') bg-yellow-100 text-yellow-800 
                                        @elseif($permission->status == 'aprobado') bg-green-100 text-green-800 
                                        @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($permission->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 whitespace-nowrap">
                                        <!-- Botón Ver -->
                                        <a href="{{ route('permissions.show', $permission->id) }}" class="text-blue-600 hover:text-blue-900">Ver</a>

                                        <!-- Botones Aprobar y Rechazar, solo visibles para el jefe -->
                                        @if(auth()->user()->hasRole('jefe'))
                                            @if($permission->status == 'pendiente')
                                                <form action="{{ route('permissions.approve', $permission->id) }}" method="POST" class="inline-block ml-2">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Aprobar</button>
                                                </form>
                                                <form action="{{ route('permissions.reject', $permission->id) }}" method="POST" class="inline-block ml-2">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Rechazar</button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
