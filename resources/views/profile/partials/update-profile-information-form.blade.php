<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Información del Perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Actualiza tu correo electrónico en caso de que haya cambiado.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input id="name" name="name" type="text" class="block w-full mt-1" :value="old('name', $user->name)" required autofocus autocomplete="name" readonly />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Correo')" />
            <x-text-input id="email" name="email" type="email" class="block w-full mt-1" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Clave del Empleado -->
        <div>
            <x-input-label for="clave_empleado" :value="__('Clave del Empleado')" />
            <x-text-input id="clave_empleado" name="clave_empleado" type="text" class="block w-full mt-1" :value="old('clave_empleado', $user->clave_empleado)" autocomplete="clave_empleado" readonly />
            <x-input-error class="mt-2" :messages="$errors->get('clave_empleado')" />
        </div>

        <!-- Fecha de Ingreso -->
        <div>
            <x-input-label for="fecha_ingreso" :value="__('Fecha de Ingreso')" />
            <x-text-input id="fecha_ingreso" name="fecha_ingreso" type="date" class="block w-full mt-1"
                :value="old('fecha_ingreso', optional($user->fecha_ingreso)->format('Y-m-d'))" readonly />
            <x-input-error class="mt-2" :messages="$errors->get('fecha_ingreso')" />
        </div>

        <!-- Puesto del Empleado -->
        <div>
            <x-input-label for="puesto_empleado" :value="__('Puesto del Empleado')" />
            <x-text-input id="puesto_empleado" name="puesto_empleado" type="text" class="block w-full mt-1" :value="old('puesto_empleado', $user->puesto_empleado)" autocomplete="puesto_empleado" readonly />
            <x-input-error class="mt-2" :messages="$errors->get('puesto_empleado')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
