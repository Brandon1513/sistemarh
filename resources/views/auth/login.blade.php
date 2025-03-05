<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" novalidate >
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo')" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input id="password" class="block w-full mt-1"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500" name="remember">
                <span class="text-sm text-gray-600 ms-2">{{ __('Recordarme') }}</span>
            </label>
        </div>
        
        <!-- Forgot Password Link -->
        <div class="flex justify-between my-5">
            <a href="{{ route('password.request') }}" 
               class="flex items-center text-sm text-purple-600 hover:text-purple-800 hover:underline">
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6 6m6 0l-6-6m-4 6h12" />
                </svg>
                {{ __('Olvidaste tu contraseña?') }}
            </a>
        </div>
        

        <!-- Login Button -->
        <x-primary-button class="justify-center w-full py-3 font-semibold text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:bg-yellow-800 active:bg-yellow-900">
            {{ __('Iniciar Sesión') }}
        </x-primary-button>
    </form>
</x-guest-layout>


