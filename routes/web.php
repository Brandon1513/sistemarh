<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\RecursosHumanosController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\EmpleadoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//rutas de permiso

Route::middleware(['auth'])->group(function () {
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::post('permissions/{permission}/approve', [PermissionController::class, 'approve'])->name('permissions.approve');
    Route::post('permissions/{permission}/reject', [PermissionController::class, 'reject'])->name('permissions.reject');
});

// rutas administrador
Route::middleware(['auth', CheckRole::class . ':administrador'])->group(function () {
    Route::resource('supervisores', \App\Http\Controllers\SupervisorController::class);
});

Route::resource('supervisores', SupervisorController::class)->parameters([
    'supervisores' => 'supervisor',
]);

//Gestionar usuarios


Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/crear', [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');

    Route::get('/empleados/{id}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{id}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
});

Route::resource('supervisores', SupervisorController::class);

//REGISTRO RH

Route::middleware(['auth', CheckRole::class . ':administrador'])->group(function () {
    Route::resource('recursoshumanos', RecursosHumanosController::class)->parameters([
        'recursoshumanos' => 'recursoshumano'
    ]);
});

Route::middleware(['role:administrador'])->group(function () {
    Route::get('/recursoshumanos/create', [RecursosHumanosController::class, 'create'])->name('recursoshumanos.create');
    Route::post('/recursoshumanos', [RecursosHumanosController::class, 'store'])->name('recursoshumanos.store');
});

//Vista de rh para los permisos
Route::middleware(['role:recursos_humanos'])->group(function () {
    Route::get('/rh', [PermissionController::class, 'indexRH'])->name('rh.index');
});

Route::get('/rh/{permission}/download', [PermissionController::class, 'downloadPDF'])->name('permissions.download');
//Descagar zip
//Route::post('/permissions/download-zip', [PermissionController::class, 'downloadPermissionsZip'])->name('permissions.download-zip');
Route::post('/permissions/download-zip', [PermissionController::class, 'downloadPDFsAsZip'])->name('permissions.download-zip');



Route::middleware(['role:recursos_humanos'])->group(function () {
    Route::get('/rh', [PermissionController::class, 'indexRH'])->name('rh.index');
    Route::get('/rh/export', [PermissionController::class, 'export'])->name('rh.export');
    Route::get('/rh/export-week', [PermissionController::class, 'exportWeek'])->name('rh.exportWeek');
});

//Control de ausencias del personal

use App\Http\Controllers\SolicitudPermisoController;

Route::middleware(['auth'])->group(function () {
    Route::get('/permisos', [SolicitudPermisoController::class, 'index'])->name('permisos.index');
    Route::get('permisos/create', [SolicitudPermisoController::class, 'create'])->name('permisos.create');
    Route::post('permisos', [SolicitudPermisoController::class, 'store'])->name('permisos.store');
    Route::get('/permisos/{id}', [SolicitudPermisoController::class, 'show'])->name('permisos.show');
    Route::post('/permisos/{id}/aprobar', [SolicitudPermisoController::class, 'aprobar'])->name('permisos.aprobar');
    Route::post('/permisos/{id}/rechazar', [SolicitudPermisoController::class, 'rechazar'])->name('permisos.rechazar');
});


Route::middleware(['auth', 'role:recursos_humanos'])->group(function () {
    Route::get('/solicitudes_permisos', [SolicitudPermisoController::class, 'indexSolicitudesPermiso'])->name('solicitudes_permisos.index');
    Route::get('/solicitudes_permisos/export', [SolicitudPermisoController::class, 'export'])->name('solicitudes_permisos.export');
    Route::get('/solicitudes_permisos/exportWeek', [SolicitudPermisoController::class, 'exportWeek'])->name('solicitudes_permisos.exportWeek');
    Route::post('/solicitudes_permisos/download-zip', [SolicitudPermisoController::class, 'downloadPDFsAsZip'])->name('solicitudes_permisos.download-zip');
    // Ruta para mostrar un permiso individual
    Route::get('/solicitudes_permisos/{id}', [SolicitudPermisoController::class, 'show'])->name('solicitudes_permisos.show');
    // Ruta para descargar el PDF de un permiso individual
    Route::get('/solicitudes_permisos/{id}/download', [SolicitudPermisoController::class, 'downloadPDF'])->name('solicitudes_permisos.download');
});


require __DIR__.'/auth.php';
