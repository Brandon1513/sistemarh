<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Entrar con Microsoft (cuentas de Dasavena) -->
    <a href="{{ route('auth.microsoft') }}"
       style="display:flex; align-items:center; justify-content:center; gap:10px; width:100%; height:46px;
              border:1.5px solid #e6d9ed; border-radius:8px; background:#fff; color:#2c1a30;
              font-size:14px; font-weight:600; text-decoration:none; transition:border-color .15s, box-shadow .15s;"
       onmouseover="this.style.borderColor='#6A2C75'; this.style.boxShadow='0 2px 8px rgba(106,44,117,.15)'"
       onmouseout="this.style.borderColor='#e6d9ed'; this.style.boxShadow='none'">
        <svg width="18" height="18" viewBox="0 0 21 21" aria-hidden="true">
            <rect x="1" y="1" width="9" height="9" fill="#f25022"/>
            <rect x="11" y="1" width="9" height="9" fill="#7fba00"/>
            <rect x="1" y="11" width="9" height="9" fill="#00a4ef"/>
            <rect x="11" y="11" width="9" height="9" fill="#ffb900"/>
        </svg>
        Entrar con Microsoft
    </a>

    <!-- Errores del inicio con Microsoft (no están ligados a ningún campo del formulario) -->
    @if ($errors->has('email') && ! old('email'))
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    @endif

    <div style="display:flex; align-items:center; gap:12px; margin:20px 0; color:#5c4a63; font-size:12px;">
        <span style="flex:1; height:1px; background:rgba(106,44,117,.25);"></span>
        o con tu correo y contraseña
        <span style="flex:1; height:1px; background:rgba(106,44,117,.25);"></span>
    </div>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo')" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" />
            @if (old('email'))
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            @endif
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