<nav x-data="{ open: false, activeDropdown: null }"
     @click.away="activeDropdown = null"
     class="nav-root">

    <style>
        :root {
            --brand:       #6A2C75;
            --brand-dark:  #541f5c;
            --brand-deep:  #3d1545;
            --brand-light: #f3eef5;
            --brand-mid:   #BBA4C0;
            --gold:        #D6A644;
            --gold-light:  #EED39B;
            --brown:       #473524;
            --rose:        #AA4969;
            --text-nav:    rgba(255,255,255,.92);
            --text-muted:  rgba(255,255,255,.6);
        }

        /* ── Base ── */
        .nav-root {
            background: var(--brand);
            border-bottom: 1px solid rgba(255,255,255,.08);
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 2px 12px rgba(61,21,69,.35);
        }

        .nav-inner {
            max-width: 80rem; margin: 0 auto;
            padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            height: 60px;
        }

        /* ── Logo ── */
        .nav-logo {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none; flex-shrink: 0;
        }
        .nav-logo-text {
            font-size: 13px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: var(--gold-light);
            display: none;
        }
        @media(min-width:640px){ .nav-logo-text { display: block; } }

        /* ── Centro: links de navegación ── */
        .nav-links {
            display: none; align-items: center; gap: 2px;
        }
        @media(min-width:640px){ .nav-links { display: flex; } }

        /* Link simple */
        .nav-link {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 6px 12px; border-radius: 8px;
            font-size: 13.5px; font-weight: 500;
            color: var(--text-nav); text-decoration: none;
            transition: background .15s, color .15s;
            position: relative;
        }
        .nav-link:hover { background: rgba(255,255,255,.1); }
        .nav-link.active {
            background: rgba(255,255,255,.15);
            font-weight: 700;
        }
        .nav-link.active::after {
            content: '';
            position: absolute; bottom: -2px; left: 12px; right: 12px;
            height: 2px; border-radius: 2px;
            background: var(--gold);
        }

        /* ── Dropdown ── */
        .nav-dropdown { position: relative; }

        .nav-dropdown-trigger {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 8px;
            font-size: 13.5px; font-weight: 500;
            color: var(--text-nav); background: transparent;
            border: none; cursor: pointer;
            transition: background .15s;
        }
        .nav-dropdown-trigger:hover { background: rgba(255,255,255,.1); }
        .nav-dropdown-trigger.open  { background: rgba(255,255,255,.15); }

        .nav-dropdown-trigger svg {
            width: 14px; height: 14px; flex-shrink: 0;
            transition: transform .2s;
            color: var(--text-muted);
        }
        .nav-dropdown-trigger.open svg { transform: rotate(180deg); }

        /* Chip de sección (ícono + label) */
        .nav-section-icon {
            width: 22px; height: 22px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .icon-admin    { background: rgba(214,166,68,.25); color: var(--gold-light); }
        .icon-sol      { background: rgba(187,164,192,.2); color: var(--brand-mid); }
        .icon-rh       { background: rgba(170,73,105,.25); color: #f0a0b8; }
        .icon-media    { background: rgba(255,255,255,.12); color: rgba(255,255,255,.8); }

        /* Panel desplegable */
        .nav-dropdown-panel {
            position: absolute; top: calc(100% + 8px); left: 0;
            min-width: 220px;
            background: #fff;
            border: 1px solid rgba(106,44,117,.12);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(61,21,69,.2), 0 2px 8px rgba(0,0,0,.08);
            padding: 6px;
            opacity: 0; pointer-events: none; transform: translateY(-6px);
            transition: opacity .15s, transform .15s;
            z-index: 100;
        }
        .nav-dropdown-panel.show {
            opacity: 1; pointer-events: auto; transform: translateY(0);
        }

        /* Header del panel */
        .panel-header {
            padding: 8px 12px 6px;
            font-size: 10px; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: var(--brand-mid);
            border-bottom: 1px solid #f0e6f5; margin-bottom: 4px;
        }

        /* Item dentro del panel */
        .panel-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            font-size: 13.5px; font-weight: 500; color: #2c1a30;
            text-decoration: none;
            transition: background .12s, color .12s;
        }
        .panel-item:hover { background: var(--brand-light); color: var(--brand); }
        .panel-item.active { background: var(--brand-light); color: var(--brand); font-weight: 700; }

        .panel-item-icon {
            width: 28px; height: 28px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 14px;
        }

        .panel-divider { height: 1px; background: #f0e6f5; margin: 4px 0; }

        /* ── Derecha: perfil ── */
        .nav-right {
            display: none; align-items: center; gap: 8px;
        }
        @media(min-width:640px){ .nav-right { display: flex; } }

        .nav-profile-btn {
            display: flex; align-items: center; gap: 8px;
            padding: 4px 10px 4px 4px; border-radius: 24px;
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.15);
            cursor: pointer; transition: background .15s; color: #fff;
            font-size: 13.5px; font-weight: 500;
        }
        .nav-profile-btn:hover { background: rgba(255,255,255,.18); }

        .nav-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--gold); color: var(--brown);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 800; flex-shrink: 0;
            overflow: hidden;
        }
        .nav-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* Panel perfil */
        .profile-panel {
            min-width: 200px; right: 0; left: auto;
        }
        .profile-header {
            padding: 12px 14px;
            border-bottom: 1px solid #f0e6f5; margin-bottom: 4px;
        }
        .profile-name  { font-size: 13px; font-weight: 700; color: #2c1a30; }
        .profile-email { font-size: 12px; color: #7a6682; margin-top: 1px; }

        .panel-item-danger { color: var(--rose); }
        .panel-item-danger:hover { background: #fce8ee; color: var(--rose); }

        /* ── Hamburger ── */
        .nav-hamburger {
            display: flex; align-items: center;
            background: transparent; border: none; cursor: pointer;
            color: #fff; padding: 6px; border-radius: 8px;
            transition: background .15s;
        }
        .nav-hamburger:hover { background: rgba(255,255,255,.1); }
        @media(min-width:640px){ .nav-hamburger { display: none; } }

        /* ── Mobile panel ── */
        .nav-mobile {
            background: var(--brand-deep);
            border-top: 1px solid rgba(255,255,255,.08);
            padding: 12px 16px 20px;
        }

        .mob-section { margin-bottom: 4px; }
        .mob-section-label {
            font-size: 10px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: var(--text-muted);
            padding: 10px 12px 4px;
        }
        .mob-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 9px;
            font-size: 14px; font-weight: 500; color: var(--text-nav);
            text-decoration: none; transition: background .12s;
        }
        .mob-link:hover, .mob-link.active { background: rgba(255,255,255,.1); }
        .mob-link .mob-icon {
            font-size: 15px; width: 22px; text-align: center;
        }
        .mob-divider { height: 1px; background: rgba(255,255,255,.08); margin: 8px 0; }

        .mob-user {
            display: flex; align-items: center; gap: 10px;
            padding: 12px; background: rgba(255,255,255,.07);
            border-radius: 10px; margin-bottom: 10px;
        }
        .mob-user-name  { font-size: 13px; font-weight: 700; color: #fff; }
        .mob-user-email { font-size: 12px; color: var(--text-muted); }
    </style>

    <div class="nav-inner">

        {{-- ── Logo ── --}}
        <a href="{{ route('dashboard') }}" class="nav-logo">
            <x-application-logo class="block w-auto h-8 fill-current" style="color:white;" />
            <span class="nav-logo-text">Intranet</span>
        </a>

        {{-- ── Links centrales (desktop) ── --}}
        @if(Auth::check())
        <div class="nav-links">

            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            {{-- Administración --}}
            @if(Auth::user()->hasAnyRole(['administrador','recursos_humanos']))
                <div class="nav-dropdown">
                    <button class="nav-dropdown-trigger {{ in_array(true, [request()->routeIs('empleados.*'), request()->routeIs('periodos.*')]) ? 'open' : '' }}"
                        @click="activeDropdown = activeDropdown === 'admin' ? null : 'admin'"
                        :class="{ 'open': activeDropdown === 'admin' }">
                        <div class="nav-section-icon icon-admin">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        Administración
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-dropdown-panel" :class="{ 'show': activeDropdown === 'admin' }">
                        <div class="panel-header">Gestión</div>
                        @if(Auth::user()->hasRole('administrador'))
                            <a href="{{ route('empleados.index') }}" class="panel-item {{ request()->routeIs('empleados.*') ? 'active' : '' }}">
                                <div class="panel-item-icon" style="background:#f3eef5;">👥</div>
                                Gestionar Usuarios
                            </a>
                            <div class="panel-divider"></div>
                        @endif
                        <a href="{{ route('periodos.index') }}" class="panel-item {{ request()->routeIs('periodos.*') ? 'active' : '' }}">
                            <div class="panel-item-icon" style="background:#faf3e0;">📅</div>
                            Gestionar Períodos
                        </a>
                    </div>
                </div>
            @endif

            {{-- Solicitudes --}}
            <div class="nav-dropdown">
                <button class="nav-dropdown-trigger"
                    @click="activeDropdown = activeDropdown === 'sol' ? null : 'sol'"
                    :class="{ 'open': activeDropdown === 'sol' }">
                    <div class="nav-section-icon icon-sol">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    Solicitudes
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div class="nav-dropdown-panel" :class="{ 'show': activeDropdown === 'sol' }">
                    <div class="panel-header">Mis solicitudes</div>
                    <a href="{{ route('permissions.index') }}" class="panel-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                        <div class="panel-item-icon" style="background:#e0eaff;">🚪</div>
                        Pase de Salida
                    </a>
                    <a href="{{ route('permisos.index') }}" class="panel-item {{ request()->routeIs('permisos.*') ? 'active' : '' }}">
                        <div class="panel-item-icon" style="background:#faf3e0;">📋</div>
                        Control de Ausencias
                    </a>
                    <a href="{{ route('vacaciones.index') }}" class="panel-item {{ request()->routeIs('vacaciones.*') ? 'active' : '' }}">
                        <div class="panel-item-icon" style="background:#e8f5ee;">🏖️</div>
                        Vacaciones
                    </a>
                </div>
            </div>

            {{-- RH --}}
            @if(Auth::user()->hasAnyRole(['recursos_humanos', 'administrador']))
                <div class="nav-dropdown">
                    <button class="nav-dropdown-trigger"
                        @click="activeDropdown = activeDropdown === 'rh' ? null : 'rh'"
                        :class="{ 'open': activeDropdown === 'rh' }">
                        <div class="nav-section-icon icon-rh">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        RH
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-dropdown-panel" :class="{ 'show': activeDropdown === 'rh' }">
                        <div class="panel-header">Recursos Humanos</div>
                        <a href="{{ route('rh.index') }}" class="panel-item {{ request()->routeIs('rh.index') ? 'active' : '' }}">
                            <div class="panel-item-icon" style="background:#fce8ee;">🗂️</div>
                            Permisos RH
                        </a>
                        <a href="{{ route('solicitudes_permisos.index') }}" class="panel-item {{ request()->routeIs('solicitudes_permisos.*') ? 'active' : '' }}">
                            <div class="panel-item-icon" style="background:#faf3e0;">📁</div>
                            Control de Ausencias
                        </a>
                        <a href="{{ route('solicitudes_vacaciones.index') }}" class="panel-item {{ request()->routeIs('solicitudes_vacaciones.*') ? 'active' : '' }}">
                            <div class="panel-item-icon" style="background:#e8f5ee;">🏖️</div>
                            Control de Vacaciones
                        </a>
                        <a href="{{ route('vacaciones.espontanea.create') }}" class="panel-item {{ request()->routeIs('vacaciones.espontanea.*') ? 'active' : '' }}">
                            <div class="panel-item-icon" style="background:#faf3e0;">📋</div>
                            Registrar Vacación Espontánea
                        </a>

                    </div>
                </div>
            @endif

            {{-- Multimedia --}}
            @if(Auth::user()->hasAnyRole(['marketing','administrador']))
                <div class="nav-dropdown">
                    <button class="nav-dropdown-trigger"
                        @click="activeDropdown = activeDropdown === 'media' ? null : 'media'"
                        :class="{ 'open': activeDropdown === 'media' }">
                        <div class="nav-section-icon icon-media">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        Multimedia
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="nav-dropdown-panel" :class="{ 'show': activeDropdown === 'media' }">
                        <div class="panel-header">Contenido</div>
                        <a href="{{ route('noticias.index') }}" class="panel-item {{ request()->routeIs('noticias.*') ? 'active' : '' }}">
                            <div class="panel-item-icon" style="background:#f3eef5;">📰</div>
                            Noticias
                        </a>
                        <a href="{{ route('carrusel.index') }}" class="panel-item {{ request()->routeIs('carrusel.*') ? 'active' : '' }}">
                            <div class="panel-item-icon" style="background:#f3eef5;">🖼️</div>
                            Carrusel
                        </a>
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Perfil derecha (desktop) ── --}}
        <div class="nav-right">
            <div class="nav-dropdown">
                <button class="nav-profile-btn"
                    @click="activeDropdown = activeDropdown === 'profile' ? null : 'profile'">
                    <div class="nav-avatar">
                        @if(Auth::user()->foto_perfil)
                            <img src="{{ asset('storage/' . Auth::user()->foto_perfil) }}" alt="">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        @endif
                    </div>
                    <span>{{ Str::limit(Auth::user()->name, 18) }}</span>
                    <svg style="width:13px;height:13px;color:rgba(255,255,255,.5);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-dropdown-panel profile-panel" :class="{ 'show': activeDropdown === 'profile' }">
                    <div class="profile-header">
                        <p class="profile-name">{{ Auth::user()->name }}</p>
                        <p class="profile-email">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="panel-item">
                        <div class="panel-item-icon" style="background:#f3eef5;">⚙️</div>
                        Mi Perfil
                    </a>
                    <div class="panel-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="panel-item panel-item-danger" style="width:100%; text-align:left; background:none; border:none; cursor:pointer;">
                            <div class="panel-item-icon" style="background:#fce8ee;">🚪</div>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Hamburger (mobile) ── --}}
        <button class="nav-hamburger" @click="open = !open">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ── Menú móvil ── --}}
    <div x-show="open" x-transition class="nav-mobile sm:hidden">
        @if(Auth::check())

            {{-- User card --}}
            <div class="mob-user">
                <div class="nav-avatar" style="width:38px;height:38px;font-size:15px;">
                    @if(Auth::user()->foto_perfil)
                        <img src="{{ asset('storage/' . Auth::user()->foto_perfil) }}" alt="">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <p class="mob-user-name">{{ Auth::user()->name }}</p>
                    <p class="mob-user-email">{{ Auth::user()->email }}</p>
                </div>
            </div>

            {{-- General --}}
            <div class="mob-section">
                <p class="mob-section-label">General</p>
                <a href="{{ route('dashboard') }}" class="mob-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="mob-icon">🏠</span> Dashboard
                </a>
            </div>

            {{-- Solicitudes --}}
            <div class="mob-divider"></div>
            <div class="mob-section">
                <p class="mob-section-label">Solicitudes</p>
                <a href="{{ route('permissions.index') }}" class="mob-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                    <span class="mob-icon">🚪</span> Pase de Salida
                </a>
                <a href="{{ route('permisos.index') }}" class="mob-link {{ request()->routeIs('permisos.*') ? 'active' : '' }}">
                    <span class="mob-icon">📋</span> Control de Ausencias
                </a>
                <a href="{{ route('vacaciones.index') }}" class="mob-link {{ request()->routeIs('vacaciones.*') ? 'active' : '' }}">
                    <span class="mob-icon">🏖️</span> Vacaciones
                </a>
            </div>

            {{-- Administración --}}
            @if(Auth::user()->hasAnyRole(['administrador','recursos_humanos']))
                <div class="mob-divider"></div>
                <div class="mob-section">
                    <p class="mob-section-label">Administración</p>
                    @if(Auth::user()->hasRole('administrador'))
                        <a href="{{ route('empleados.index') }}" class="mob-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}">
                            <span class="mob-icon">👥</span> Gestionar Usuarios
                        </a>
                    @endif
                    <a href="{{ route('periodos.index') }}" class="mob-link {{ request()->routeIs('periodos.*') ? 'active' : '' }}">
                        <span class="mob-icon">📅</span> Gestionar Períodos
                    </a>
                </div>
            @endif

            {{-- RH --}}
            @if(Auth::user()->hasAnyRole(['recursos_humanos','administrador']))
                <div class="mob-divider"></div>
                <div class="mob-section">
                    <p class="mob-section-label">Recursos Humanos</p>
                    <a href="{{ route('rh.index') }}" class="mob-link {{ request()->routeIs('rh.index') ? 'active' : '' }}">
                        <span class="mob-icon">🗂️</span> Permisos RH
                    </a>
                    <a href="{{ route('solicitudes_permisos.index') }}" class="mob-link {{ request()->routeIs('solicitudes_permisos.*') ? 'active' : '' }}">
                        <span class="mob-icon">📁</span> Control de Ausencias
                    </a>
                    <a href="{{ route('solicitudes_vacaciones.index') }}" class="mob-link {{ request()->routeIs('solicitudes_vacaciones.*') ? 'active' : '' }}">
                        <span class="mob-icon">🏖️</span> Control de Vacaciones
                    </a>
                    <a href="{{ route('vacaciones.espontanea.create') }}" class="mob-link {{ request()->routeIs('vacaciones.espontanea.*') ? 'active' : '' }}">
                        <span class="mob-icon">📋</span> Vacación Espontánea
                    </a>

                </div>
            @endif

            {{-- Multimedia --}}
            @if(Auth::user()->hasAnyRole(['marketing','administrador']))
                <div class="mob-divider"></div>
                <div class="mob-section">
                    <p class="mob-section-label">Multimedia</p>
                    <a href="{{ route('noticias.index') }}" class="mob-link {{ request()->routeIs('noticias.*') ? 'active' : '' }}">
                        <span class="mob-icon">📰</span> Noticias
                    </a>
                    <a href="{{ route('carrusel.index') }}" class="mob-link {{ request()->routeIs('carrusel.*') ? 'active' : '' }}">
                        <span class="mob-icon">🖼️</span> Carrusel
                    </a>
                </div>
            @endif

            {{-- Cuenta --}}
            <div class="mob-divider"></div>
            <div class="mob-section">
                <p class="mob-section-label">Cuenta</p>
                <a href="{{ route('profile.edit') }}" class="mob-link">
                    <span class="mob-icon">⚙️</span> Mi Perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mob-link" style="width:100%; text-align:left; background:none; border:none; cursor:pointer; color:rgba(255,180,180,.9);">
                        <span class="mob-icon">🚪</span> Cerrar Sesión
                    </button>
                </form>
            </div>

        @else
            <div style="padding:16px;">
                <a href="{{ route('login') }}" class="mob-link">Iniciar Sesión</a>
            </div>
        @endif
    </div>
</nav>