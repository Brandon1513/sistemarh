@php
    $editando = $dia->exists;
    // En edición, la fecha se arma con el año guardado (o el actual, si se repite cada año)
    $fechaActual = $editando ? sprintf('%04d-%02d-%02d', $dia->anio ?? now()->year, $dia->mes, $dia->dia) : '';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dias_inhabiles.index') }}" style="color:#BBA4C0;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $editando ? 'Editar día inhábil' : 'Agregar día inhábil' }}</h2>
        </div>
    </x-slot>

    <style>
        :root {
            --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0;
            --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682;
        }
        .form-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }
        .form-section { padding:26px 32px; display:flex; flex-direction:column; gap:20px; }
        .form-footer { padding:20px 32px; background:var(--beige-light); border-top:1.5px solid var(--border); display:flex; justify-content:flex-end; gap:12px; }
        .field { display:flex; flex-direction:column; gap:5px; }
        .field-label { font-size:13px; font-weight:600; color:var(--brown); }
        .req { color:var(--rose); }
        .field-input, .field-select { height:42px; padding:0 14px; border:1.5px solid var(--border); border-radius:9px; font-size:14px; color:var(--text); background:#fff; width:100%; }
        .field-input:focus, .field-select:focus { border-color:var(--brand); outline:none; box-shadow:0 0 0 3px rgba(106,44,117,.12); }
        .field-hint { font-size:12px; color:var(--muted); }
        .field-error { font-size:12px; color:var(--rose); }
        .check { display:flex; align-items:center; gap:10px; padding:12px 14px; border:1.5px solid var(--border); border-radius:9px; cursor:pointer; }
        .check input { width:18px; height:18px; accent-color:var(--brand); }
        .check span { font-size:14px; color:var(--text); }
        .btn { display:inline-flex; align-items:center; gap:7px; padding:0 22px; height:42px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; }
        .btn-primary { background:var(--brand); color:#fff; }
        .btn-ghost { background:transparent; color:var(--muted); border:1.5px solid var(--border); }
    </style>

    <div class="py-10" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="form-card">
                <form method="POST" action="{{ $editando ? route('dias_inhabiles.update', $dia) : route('dias_inhabiles.store') }}">
                    @csrf
                    @if($editando) @method('PUT') @endif

                    <div class="form-section">
                        <div class="field">
                            <label class="field-label" for="nombre">Nombre <span class="req">*</span></label>
                            <input id="nombre" name="nombre" type="text" class="field-input" maxlength="100" required
                                value="{{ old('nombre', $dia->nombre) }}" placeholder="Ej. Virgen de Zapopan, Puente de Día de Muertos">
                            @error('nombre')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>

                        <div class="field">
                            <label class="field-label" for="fecha">Fecha <span class="req">*</span></label>
                            <input id="fecha" name="fecha" type="date" class="field-input" required
                                value="{{ old('fecha', $fechaActual) }}">
                            @error('fecha')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>

                        <label class="check">
                            <input type="hidden" name="recurrente" value="0">
                            <input type="checkbox" name="recurrente" value="1"
                                {{ old('recurrente', $editando ? (int) is_null($dia->anio) : 0) ? 'checked' : '' }}>
                            <span>Se repite <strong>cada año</strong> en esta fecha</span>
                        </label>
                        <span class="field-hint" style="margin-top:-12px;">
                            Márcalo para días como el 12 de octubre. Déjalo sin marcar para un puente de un solo año.
                        </span>

                        <div class="field">
                            <label class="field-label" for="sede_id">Aplica a</label>
                            <select id="sede_id" name="sede_id" class="field-select">
                                <option value="">Todas las sedes</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}" {{ (string) old('sede_id', $dia->sede_id) === (string) $sede->id ? 'selected' : '' }}>
                                        Solo {{ $sede->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sede_id')<span class="field-error">⚠ {{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-footer">
                        <a href="{{ route('dias_inhabiles.index') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">{{ $editando ? 'Guardar cambios' : 'Agregar' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>