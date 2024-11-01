<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                    </a>
                </div>

                <!-- Navigation Links (Visible solo para usuarios logueados) -->
                @if (Auth::check())
                    <div class="hidden space-x-8 sm:flex sm:items-center sm:ms-6">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        @if (Auth::user()->hasRole('administrador'))
                            <!-- Dropdown para "Gestionar Jefes" y "Gestionar RH" -->
                            <div class="relative">
                                <x-dropdown align="left">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                                            <div>{{ __('Administración') }}</div>
                                            <div class="ms-1">
                                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <!-- Gestionar Jefes -->
                                        <x-dropdown-link :href="route('supervisores.index')">
                                            {{ __('Gestionar Jefes') }}
                                        </x-dropdown-link>
                                        <!-- Gestionar RH -->
                                        <x-dropdown-link :href="route('recursoshumanos.index')">
                                            {{ __('Gestionar RH') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('periodos.index')">
                                            {{ __('Gestionar periodos') }}
                                        </x-dropdown-link>
                                        <x-dropdown-link :href="route('empleados.index')">
                                            {{ __('Gestionar Usuarios') }}
                                        </x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endif
                        <div class="relative">
                            <x-dropdown align="left">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                                        <div>{{ __('Solicitudes') }}</div>
                                        <div class="ms-1">
                                            <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Gestionar Jefes -->
                                    <x-dropdown-link :href="route('permissions.index')" :active="request()->routeIs('permissions.index')">
                                        {{ __('Pase de salida') }}
                                    </x-dropdown-link>
                                    <!-- Gestionar RH -->
                                    <x-dropdown-link :href="route('permisos.index')" :active="request()->routeIs('permisos.index')">
                                        {{ __('Control de ausencias') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('vacaciones.index')" :active="request()->routeIs('vacaciones.index')">
                                        {{ __('Solicitar Vacaciones') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @if (Auth::user()->hasRole('recursos_humanos'))
                        <div class="relative">
                            <x-dropdown align="left">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                                        <div>{{ __('RH') }}</div>
                                        <div class="ms-1">
                                            <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <!-- Gestionar Jefes -->
                                    <x-dropdown-link :href="route('rh.index')" :active="request()->routeIs('rh.index')">
                                        {{ __('Permisos RH') }}
                                    </x-dropdown-link>
                                    <!-- Gestionar RH -->
                                    <x-dropdown-link :href="route('solicitudes_permisos.index')" :active="request()->routeIs('solicitudes_permisos.index')">
                                        {{ __('Control de ausencia') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('dashboard')">
                                        {{ __('Vacaciones Proximamente') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                        @endif
                        
                    </div>
                @endif
            </div>

            <!-- Settings Dropdown (Solo si está logueado) -->
            @if (Auth::check())
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Salir') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <!-- Mostrar link de iniciar sesión si no está logueado -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Iniciar Sesión</a>
                </div>
            @endif

            <!-- Hamburger Menu (para móvil) -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options (solo si está logueado) -->
        @if (Auth::check())
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('permissions.index')" :active="request()->routeIs('permissions.index')">
                        {{ __('Permisos') }}
                    </x-responsive-nav-link>
                    
                    
                    @if(Auth::user()->hasRole('administrador'))
                        <x-responsive-nav-link :href="route('empleados.index')" :active="request()->routeIs('empleados.index')">
                        {{ __('Gestionar Usuarios') }}
                        </x-responsive-nav-link>
                    @endif

                    @if(Auth::user()->hasRole('administrador'))
                        <x-responsive-nav-link :href="route('supervisores.index')" :active="request()->routeIs('supervisores.index')">
                        {{ __('Gestionar Jefes') }}
                        </x-responsive-nav-link>
                    @endif
                    
                    @if(Auth::user()->hasRole('administrador'))
                        <x-responsive-nav-link :href="route('recursoshumanos.index')" :active="request()->routeIs('recursoshumanos.index')">
                        {{ __('Gestionar RH') }}
                        </x-responsive-nav-link>
                    @endif  

                    @if(Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('rh.index')" :active="request()->routeIs('rh.index')">
                            {{ __('Permisos RH') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('solicitudes_permisos.index')" :active="request()->routeIs('solicitudes_permisos.index')">
                            {{ __('Control ausencias RH') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('rh.index')" :active="request()->routeIs('rh.index')">
                            {{ __('Vacaciones RH Proximamente') }}
                        </x-responsive-nav-link>
                    @endif
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Salir') }}
                            </x-responsive-nav-link>
                        </form>
                </div>
            </div>
        @else
            <!-- Mostrar link de iniciar sesión si no está logueado en menú responsive -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Iniciar Sesión') }}
                </x-responsive-nav-link>
            </div>
        @endif
    </div>
</nav>
