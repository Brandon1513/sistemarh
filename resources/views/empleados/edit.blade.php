<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-image: url('{{ asset('images/background-pattern.png') }}');">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Título del formulario -->
                    <h2 class="mb-6 text-lg font-bold text-gray-700">{{ __('Modificar información del usuario') }}</h2>

                    <!-- Formulario de Edición -->
                    <form method="POST" action="{{ route('empleados.update', $user->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nombre Completo -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nombre Completo')" />
                            <x-text-input id="name" class="block w-full mt-1" type="text" name="name" value="{{ $user->name }}" required autofocus autocomplete="name" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <!-- Foto de Perfil Actual -->
                        @if ($user->foto_perfil)
                            <div class="mb-4">
                                <x-input-label :value="__('Foto de perfil actual')" />
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $user->foto_perfil) }}" alt="Foto de perfil" class="w-32 h-32 rounded-full object-cover border">
                                </div>
                            </div>
                        @endif
                        <!-- Subir Nueva Foto de Perfil -->
                        <div class="mb-4">
                            <x-input-label for="foto_perfil" :value="__('Actualizar Foto de perfil (Opcional)')" />
                            <input id="foto_perfil" name="foto_perfil" type="file" accept="image/*"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                            <x-input-error :messages="$errors->get('foto_perfil')" class="mt-2" />
                        </div>


                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Correo')" />
                            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" value="{{ $user->email }}" required autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Clave del Empleado -->
                        <div class="mb-4">
                            <x-input-label for="clave_empleado" :value="__('Clave del Empleado')" />
                            <x-text-input id="clave_empleado" class="block w-full mt-1" type="text" name="clave_empleado" value="{{ $user->clave_empleado }}" required autocomplete="clave_empleado" />
                            <x-input-error :messages="$errors->get('clave_empleado')" class="mt-2" />
                        </div>

                        <!-- Fecha de Ingreso -->
                      
                            <div class="mb-4">
                                <x-input-label for="fecha_ingreso" :value="__('Fecha de Ingreso')" />
                                <x-text-input id="fecha_ingreso" class="block w-full mt-1" type="date" name="fecha_ingreso" 
                                    :value="$user->fecha_ingreso ? \Carbon\Carbon::parse($user->fecha_ingreso)->format('Y-m-d') : ''" required />
                                <x-input-error :messages="$errors->get('fecha_ingreso')" class="mt-2" />
                            </div>


                        <!-- Puesto del Empleado -->
                        <div class="mb-4">
                            <x-input-label for="puesto_empleado" :value="__('Puesto del Empleado')" />
                            <x-text-input id="puesto_empleado" class="block w-full mt-1" type="text" name="puesto_empleado" value="{{ $user->puesto_empleado }}" required />
                            <x-input-error :messages="$errors->get('puesto_empleado')" class="mt-2" />
                        </div>

                        <!-- Departamento -->
                        <div class="mb-4">
                            <x-input-label for="departamento_id" :value="__('Departamento')" />
                            <select name="departamento_id" id="departamento_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                @foreach ($departamentos as $departamento)
                                    <option value="{{ $departamento->id }}" {{ $user->departamento_id == $departamento->id ? 'selected' : '' }}>
                                        {{ $departamento->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Selección de Supervisor -->
                        <div class="mb-4">
                            <x-input-label for="supervisor_id" :value="__('Supervisor')" />
                            <select id="supervisor_id" name="supervisor_id" class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">{{ __('Seleccione un supervisor') }}</option>
                                @foreach ($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}" {{ $user->supervisor_id == $supervisor->id ? 'selected' : '' }}>
                                        {{ $supervisor->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('supervisor_id')" class="mt-2" />
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Nueva Contraseña (opcional)')" />
                            <x-text-input id="password" class="block w-full mt-1" type="password" name="password" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Confirmar Nueva Contraseña')" />
                            <x-text-input id="password_confirmation" class="block w-full mt-1" type="password" name="password_confirmation" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                        
                        <!-- Roles -->
                        <div class="mt-4">
                            <x-input-label for="roles" :value="__('Roles')" />
                            @foreach ($roles as $role)
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                        class="text-indigo-600 transition duration-150 ease-in-out form-checkbox"
                                        {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                        <span class="ml-2">{{ $role->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        

                        <!-- Botón de Actualizar -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar Usuario') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
