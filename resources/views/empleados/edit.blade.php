<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('empleados.index') }}" class="transition" style="color:#BBA4C0;" onmouseover="this.style.color='#6A2C75'" onmouseout="this.style.color='#BBA4C0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Editar Usuario</h2>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:       #6A2C75;
            --brand-dark:  #541f5c;
            --brand-light: #f3eef5;
            --brand-mid:   #BBA4C0;
            --gold:        #D6A644;
            --gold-light:  #EED39B;
            --brown:       #473524;
            --rose:        #AA4969;
            --beige-light: #f7f3ef;
            --border:      #e6d9ed;
            --text:        #2c1a30;
            --muted:       #7a6682;
            --error:       #AA4969;
            --error-bg:    #fce8ee;
        }

        .form-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }

        /* Banner */
        .user-banner {
            padding:28px 32px; display:flex; align-items:center; gap:20px;
            position:relative; overflow:hidden;
            background:linear-gradient(135deg, var(--brand) 0%, #9b4dab 100%);
        }
        .user-banner::before { content:''; position:absolute; top:-50px; right:-50px; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.06); }
        .user-banner::after  { content:''; position:absolute; bottom:-60px; right:80px; width:180px; height:180px; border-radius:50%; background:rgba(214,166,68,.12); }
        .banner-avatar { width:68px; height:68px; border-radius:50%; border:3px solid rgba(255,255,255,.35); object-fit:cover; flex-shrink:0; z-index:1; }
        .banner-placeholder { width:68px; height:68px; border-radius:50%; border:3px solid rgba(255,255,255,.35); background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:800; color:#fff; flex-shrink:0; z-index:1; }
        .banner-info { z-index:1; }
        .banner-name { font-size:20px; font-weight:700; color:#fff; line-height:1.2; }
        .banner-sub  { font-size:13px; color:rgba(255,255,255,.75); margin-top:2px; }
        .banner-badge { display:inline-flex; align-items:center; gap:5px; background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.3); border-radius:20px; padding:3px 10px; font-size:11px; font-weight:600; color:#fff; margin-top:8px; }
        .banner-badge .dot { width:6px; height:6px; border-radius:50%; background:#EED39B; }

        .form-section { padding:28px 32px; border-bottom:1px solid #f0e6f5; }
        .form-section:last-child { border-bottom:none; }
        .form-section-footer { padding:20px 32px; background:var(--beige-light); border-top:1.5px solid var(--border); }

        .section-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--brand); margin-bottom:20px; display:flex; align-items:center; gap:8px; }
        .section-title::after { content:''; flex:1; height:1px; background:linear-gradient(to right,#e0c8e8,transparent); }

        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        @media(max-width:640px){ .form-grid{grid-template-columns:1fr;} }
        .col-span-2 { grid-column:span 2; }
        @media(max-width:640px){ .col-span-2{grid-column:span 1;} }

        .field { display:flex; flex-direction:column; gap:5px; }
        .field-label { font-size:13px; font-weight:600; color:var(--brown); display:flex; align-items:center; gap:5px; }
        .field-label .req { color:var(--rose); }
        .field-label .opt { font-size:11px; font-weight:400; color:var(--muted); background:#f0e6f5; border-radius:4px; padding:1px 7px; }

        .field-input,.field-select { height:42px; padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; outline:none; transition:border-color .2s,box-shadow .2s; width:100%; }
        .field-input:focus,.field-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-input.err { border-color:var(--error); background:var(--error-bg); }
        .field-error { font-size:12px; color:var(--error); }
        .field-hint  { font-size:12px; color:var(--muted); display:flex; align-items:center; gap:4px; }

        .input-wrap { position:relative; }
        .input-wrap .field-input { padding-right:44px; }
        .eye-btn { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; padding:4px; color:var(--muted); border-radius:6px; transition:color .15s,background .15s; display:flex; align-items:center; }
        .eye-btn:hover { color:var(--brand); background:var(--brand-light); }

        /* Foto edit */
        .photo-row { display:flex; align-items:center; gap:20px; flex-wrap:wrap; }
        .photo-preview { width:72px; height:72px; border-radius:50%; object-fit:cover; border:3px solid var(--brand-light); }
        .photo-placeholder { width:72px; height:72px; border-radius:50%; background:linear-gradient(135deg,var(--brand),var(--brand-mid)); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; color:#fff; }
        .upload-btn { display:inline-flex; align-items:center; gap:7px; padding:8px 16px; border:1.5px dashed var(--border); border-radius:9px; cursor:pointer; font-size:13px; color:var(--muted); background:#f9f5fb; transition:all .15s; position:relative; }
        .upload-btn:hover { border-color:var(--brand); color:var(--brand); background:var(--brand-light); }
        .upload-btn input { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }

        /* Roles */
        .roles-grid { display:flex; flex-wrap:wrap; gap:10px; }
        .role-chip { display:flex; align-items:center; gap:7px; padding:7px 14px; border:1.5px solid var(--border); border-radius:8px; cursor:pointer; transition:all .15s; user-select:none; }
        .role-chip input { display:none; }
        .role-chip:hover { border-color:var(--brand); background:var(--brand-light); }
        .role-chip.selected { border-color:var(--brand); background:var(--brand-light); }
        .chip-dot { width:10px; height:10px; border-radius:50%; border:2px solid var(--border); transition:all .15s; }
        .role-chip.selected .chip-dot { background:var(--brand); border-color:var(--brand); }
        .role-chip span { font-size:13px; font-weight:600; color:var(--brown); }

        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; transition:all .18s; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 14px rgba(106,44,117,.3); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
        .btn-ghost:hover { border-color:var(--brand-mid); color:var(--brand); }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="form-card">

                {{-- Banner --}}
                <div class="user-banner">
                    @if($user->foto_perfil)
                        <img id="banner-img" src="{{ asset('storage/' . $user->foto_perfil) }}" alt="{{ $user->name }}" class="banner-avatar">
                    @else
                        <div class="banner-placeholder" id="banner-placeholder">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    @endif
                    <div class="banner-info">
                        <p class="banner-name">{{ $user->name }}</p>
                        <p class="banner-sub">{{ $user->puesto_empleado }} · {{ $user->departamento->name ?? '' }}</p>
                        <div class="banner-badge">
                            <span class="dot"></span>
                            {{ $user->activo ? 'Activo' : 'Inactivo' }}
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('empleados.update', $user->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Información Personal --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Información Personal
                        </p>
                        <div class="form-grid">
                            <div class="col-span-2 field">
                                <label class="field-label" for="name">Nombre Completo <span class="req">*</span></label>
                                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                                    class="field-input {{ $errors->has('name') ? 'err' : '' }}" required autofocus>
                                @error('name')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="col-span-2 field">
                                <label class="field-label">Foto de Perfil <span class="opt">Opcional</span></label>
                                <div class="photo-row">
                                    <div>
                                        @if($user->foto_perfil)
                                            <img id="photo-preview" src="{{ asset('storage/' . $user->foto_perfil) }}" class="photo-preview">
                                        @else
                                            <div class="photo-placeholder" id="photo-preview">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="upload-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            Cambiar foto
                                            <input type="file" name="foto_perfil" accept="image/*" onchange="previewEdit(event)">
                                        </label>
                                        <p id="photo-fname" style="font-size:12px;color:var(--muted);margin-top:5px;"></p>
                                    </div>
                                </div>
                                @error('foto_perfil')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- Información Laboral --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Información Laboral
                        </p>
                        <div class="form-grid">
                            <div class="field">
                                <label class="field-label" for="clave_empleado">Clave de Empleado <span class="req">*</span></label>
                                <input id="clave_empleado" name="clave_empleado" type="text"
                                    value="{{ old('clave_empleado', $user->clave_empleado) }}"
                                    class="field-input {{ $errors->has('clave_empleado') ? 'err' : '' }}" required>
                                @error('clave_empleado')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="fecha_ingreso">Fecha de Ingreso <span class="req">*</span></label>
                                <input id="fecha_ingreso" name="fecha_ingreso" type="date"
                                    value="{{ old('fecha_ingreso', $user->fecha_ingreso ? \Carbon\Carbon::parse($user->fecha_ingreso)->format('Y-m-d') : '') }}"
                                    class="field-input {{ $errors->has('fecha_ingreso') ? 'err' : '' }}" required>
                                @error('fecha_ingreso')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="col-span-2 field">
                                <label class="field-label" for="puesto_empleado">Puesto <span class="req">*</span></label>
                                <input id="puesto_empleado" name="puesto_empleado" type="text"
                                    value="{{ old('puesto_empleado', $user->puesto_empleado) }}"
                                    class="field-input {{ $errors->has('puesto_empleado') ? 'err' : '' }}" required>
                                @error('puesto_empleado')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="departamento_id">Departamento <span class="req">*</span></label>
                                <select name="departamento_id" id="departamento_id"
                                    class="field-select {{ $errors->has('departamento_id') ? 'err' : '' }}" required>
                                    @foreach ($departamentos as $dep)
                                        <option value="{{ $dep->id }}" {{ old('departamento_id', $user->departamento_id) == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                                    @endforeach
                                </select>
                                @error('departamento_id')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="supervisor_id">Supervisor <span class="opt">Opcional</span></label>
                                <select name="supervisor_id" id="supervisor_id" class="field-select">
                                    <option value="">Sin supervisor</option>
                                    @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $user->supervisor_id) == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Acceso y Seguridad --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            Acceso y Seguridad
                        </p>
                        <div class="form-grid">
                            <div class="col-span-2 field">
                                <label class="field-label" for="email">Correo Electrónico <span class="req">*</span></label>
                                <input id="email" name="email" type="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="field-input {{ $errors->has('email') ? 'err' : '' }}" required>
                                @error('email')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="password">Nueva Contraseña <span class="opt">Opcional</span></label>
                                <div class="input-wrap">
                                    <input id="password" name="password" type="password"
                                        class="field-input {{ $errors->has('password') ? 'err' : '' }}"
                                        placeholder="Dejar vacío para no cambiar" autocomplete="new-password">
                                    <button type="button" class="eye-btn" onclick="togglePwd('password',this)">
                                        <svg id="eye-password" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <span class="field-hint">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Solo completa si deseas cambiar la contraseña
                                </span>
                                @error('password')<span class="field-error">⚠ {{ $message }}</span>@enderror
                            </div>
                            <div class="field">
                                <label class="field-label" for="password_confirmation">Confirmar Nueva Contraseña</label>
                                <div class="input-wrap">
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="field-input" placeholder="Repite la nueva contraseña" autocomplete="new-password">
                                    <button type="button" class="eye-btn" onclick="togglePwd('password_confirmation',this)">
                                        <svg id="eye-password_confirmation" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Roles --}}
                    <div class="form-section">
                        <p class="section-title">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Roles y Permisos
                        </p>
                        <div class="roles-grid">
                            @foreach ($roles as $role)
                                @php $hasRole = $user->hasRole($role->name); @endphp
                                <label class="role-chip {{ $hasRole ? 'selected' : '' }}">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                        {{ $hasRole ? 'checked' : '' }}
                                        onchange="this.closest('.role-chip').classList.toggle('selected', this.checked)">
                                    <span class="chip-dot"></span>
                                    <span>{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="form-section-footer">
                        <div style="display:flex; align-items:center; justify-content:flex-end; gap:12px;">
                            <a href="{{ route('empleados.index') }}" class="btn btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePwd(id, btn) {
            const input = document.getElementById(id);
            const icon  = document.getElementById('eye-' + id);
            const show  = input.type === 'password';
            input.type  = show ? 'text' : 'password';
            icon.innerHTML = show
                ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
                : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
        function previewEdit(e) {
            const file = e.target.files[0];
            if (!file) return;
            const preview = document.getElementById('photo-preview');
            if (preview && preview.tagName === 'IMG') preview.src = URL.createObjectURL(file);
            document.getElementById('photo-fname').textContent = '📎 ' + file.name;
            // Actualizar banner también
            const bannerImg = document.getElementById('banner-img');
            if (bannerImg) bannerImg.src = URL.createObjectURL(file);
        }
    </script>
</x-app-layout>