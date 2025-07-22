<?php

use App\Models\Noticia;
use App\Models\CarruselImage;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarruselController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\PeriodoVacacionController;
use App\Http\Controllers\RecursosHumanosController;
use App\Http\Controllers\SolicitudPermisoController;
use App\Http\Controllers\SolicitudVacacionController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    $noticias = Noticia::where('activo', 1)->latest()->take(6)->get();
    $carruselImages = CarruselImage::all();

    return view('dashboard', compact('noticias', 'carruselImages'));
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
    Route::patch('/empleados/{id}/toggle', [EmpleadoController::class, 'toggle'])->name('empleados.toggle');

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
Route::post('/permissions/download-zip', [PermissionController::class, 'downloadPDFsAsZip'])->name('permissions.download-zip');



Route::middleware(['role:recursos_humanos'])->group(function () {
    Route::get('/rh', [PermissionController::class, 'indexRH'])->name('rh.index');
    Route::get('/rh/export', [PermissionController::class, 'export'])->name('rh.export');
    Route::get('/rh/export-week', [PermissionController::class, 'exportWeek'])->name('rh.exportWeek');
});

//Control de ausencias del personal




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
    Route::get('/solicitudes_permisos/download-zip', [SolicitudPermisoController::class, 'downloadSolicitudesZIP'])->name('solicitudes_permisos.download-zip');
    // Ruta para mostrar un permiso individual
    Route::get('/solicitudes_permisos/{id}', [SolicitudPermisoController::class, 'show'])->name('solicitudes_permisos.show');
    // Ruta para descargar el PDF de un permiso individual
    Route::get('/solicitudes_permisos/{id}/download', [SolicitudPermisoController::class, 'downloadPDF'])
    ->name('solicitudes_permisos.download');


    Route::get('/rh/permisos', [SolicitudPermisoController::class, 'indexRH'])->name('rh.permisos.index');
});


//VACACIONES
// Listar todas las solicitudes de vacaciones (para admin o supervisores)
Route::middleware(['auth'])->group(function(){
    Route::get('/vacaciones', [SolicitudVacacionController::class, 'index'])->name('vacaciones.index');

    // Formulario para crear una nueva solicitud de vacaciones
    Route::get('/vacaciones/create', [SolicitudVacacionController::class, 'create'])->name('vacaciones.create');
    
    // Guardar la nueva solicitud de vacaciones
    Route::post('/vacaciones', [SolicitudVacacionController::class, 'store'])->name('vacaciones.store');
    
    // Ver una solicitud específica de vacaciones
    Route::get('/vacaciones/{id}', [SolicitudVacacionController::class, 'show'])->name('vacaciones.show');
    
    Route::get('/vacaciones/{id}/aprobar', [SolicitudVacacionController::class, 'approve'])->name('vacaciones.aprobar');
    Route::get('/vacaciones/{id}/rechazar', [SolicitudVacacionController::class, 'reject'])->name('vacaciones.rechazar');
    Route::delete('/vacaciones/{vacacione}', [SolicitudVacacionController::class, 'destroy'])->name('vacaciones.destroy');
    
});

// Rutas para las solicitudes de vacaciones


Route::middleware(['auth', 'role:recursos_humanos'])->group(function () {
    Route::get('/solicitudes_vacaciones', [SolicitudVacacionController::class, 'indexRH'])->name('solicitudes_vacaciones.index');
    Route::get('/solicitudes_vacaciones/{id}', [SolicitudVacacionController::class, 'showRH'])->name('solicitudes_vacaciones.show');
    Route::get('/solicitudes_vacaciones/{id}/download', [SolicitudVacacionController::class, 'downloadPDF'])->name('solicitudes_vacaciones.download');
    Route::get('/solicitudes_vacaciones/export', [SolicitudVacacionController::class, 'export'])->name('solicitudes_vacaciones.export');

    Route::get('/solicitudes_vacaciones/exportWeek', [SolicitudVacacionController::class, 'exportWeek'])->name('solicitudes_vacaciones.exportWeek');
    Route::get('/solicitudes_vacaciones/download-zip', [SolicitudVacacionController::class, 'download-zip'])->name('solicitudes_vacaciones.download-zip');
    
});

//PERIODOS
Route::middleware(['auth', 'role:administrador|recursos_humanos'])->group(function () {
    Route::get('/periodos/create', [PeriodoVacacionController::class, 'create'])->name('periodos.create');
    Route::post('/periodos/store', [PeriodoVacacionController::class, 'store'])->name('periodos.store');
    Route::post('/calculate-days', [PeriodoVacacionController::class, 'calculateDays'])->name('calculate.days');
    Route::get('/periodos', [PeriodoVacacionController::class, 'index'])->name('periodos.index');
    // Editar un período de vacaciones existente
    Route::get('/periodos/{id}/editar', [PeriodoVacacionController::class, 'edit'])->name('periodos.edit');
    // Actualizar un período de vacaciones
    Route::put('/periodos/{id}', [PeriodoVacacionController::class, 'update'])->name('periodos.update');
    // Eliminar un período de vacaciones
    Route::delete('/periodos/{id}', [PeriodoVacacionController::class, 'destroy'])->name('periodos.destroy');
    Route::post('/periodos_vacaciones/{id}/toggle-activo', [PeriodoVacacionController::class, 'toggleActivo'])->name('periodos_vacaciones.toggle-activo');

    Route::get('/periodos/export', [PeriodoVacacionController::class, 'export'])->name('periodos.export');

});

//Noticias

Route::middleware(['auth', 'role:administrador|marketing'])->group(function(){
    Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias.index');
    Route::patch('noticias/{noticia}/toggle', [NoticiaController::class, 'toggle'])->name('noticias.toggle');
    Route::get('/noticias/create', [NoticiaController::class, 'create'])->name('noticias.create');
    Route::post('/noticias', [NoticiaController::class, 'store'])->name('noticias.store');
    Route::get('/noticias/{noticia}/edit', [NoticiaController::class, 'edit'])->name('noticias.edit');
    Route::put('/noticias/{noticia}', [NoticiaController::class, 'update'])->name('noticias.update');
    Route::delete('/noticias/{noticia}', [NoticiaController::class, 'destroy'])->name('noticias.destroy');
    // Noticias públicas (accesibles sin autenticación)
    Route::get('/noticias-publicas', [NoticiaController::class, 'publicIndex'])->name('noticias.public');

// Ruta para ver una noticia específica en formato JSON
    Route::get('/noticias/{noticia}', function (Noticia $noticia) {
    return response()->json($noticia);
    });
});
//Carrusel


Route::middleware(['auth', 'role:administrador|marketing'])->group(function () {
    Route::resource('carrusel', CarruselController::class)->only(['index', 'create', 'store']);
    Route::delete('/carrusel/{id}', [CarruselController::class, 'destroy'])->name('carrusel.destroy');
    Route::put('/carrusel/{id}', [CarruselController::class, 'update'])->name('carrusel.update');

});





require __DIR__.'/auth.php';
