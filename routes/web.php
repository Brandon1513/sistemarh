<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\RecursosHumanosController;
use App\Http\Controllers\Auth\RegisteredUserController;

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


    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

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




require __DIR__.'/auth.php';
