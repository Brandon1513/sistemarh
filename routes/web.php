<?php

use App\Models\Noticia;
use App\Models\CarruselImage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarruselController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PeriodoVacacionController;
use App\Http\Controllers\SolicitudPermisoController;
use App\Http\Controllers\SolicitudVacacionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $noticias = Noticia::with('galeria')->where('activo', 1)->latest()->take(6)->get();
    $carruselImages = CarruselImage::all();

    return view('dashboard', compact('noticias', 'carruselImages'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Pases de entrada y salida (employee_permissions)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::post('permissions/{permission}/approve', [PermissionController::class, 'approve'])->name('permissions.approve');
    Route::post('permissions/{permission}/reject', [PermissionController::class, 'reject'])->name('permissions.reject');
});

// Vista de RH para los pases (incluye descargas, que antes no pedían sesión)
Route::middleware(['auth', 'role:recursos_humanos'])->group(function () {
    Route::get('/rh', [PermissionController::class, 'indexRH'])->name('rh.index');
    Route::get('/rh/export', [PermissionController::class, 'export'])->name('rh.export');
    Route::get('/rh/export-week', [PermissionController::class, 'exportWeek'])->name('rh.exportWeek');
    Route::get('/rh/{permission}/download', [PermissionController::class, 'downloadPDF'])->name('permissions.download');
    Route::post('/permissions/download-zip', [PermissionController::class, 'downloadPDFsAsZip'])->name('permissions.download-zip');
});

/*
|--------------------------------------------------------------------------
| Gestión de empleados
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/crear', [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{id}/edit', [EmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{id}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    Route::patch('/empleados/{id}/toggle', [EmpleadoController::class, 'toggle'])->name('empleados.toggle');
});

/*
|--------------------------------------------------------------------------
| Permisos de ausencia (solicitudes_permisos)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/permisos', [SolicitudPermisoController::class, 'index'])->name('permisos.index');
    Route::get('permisos/create', [SolicitudPermisoController::class, 'create'])->name('permisos.create');
    Route::post('permisos', [SolicitudPermisoController::class, 'store'])->name('permisos.store');
    Route::get('/permisos/{id}', [SolicitudPermisoController::class, 'show'])->name('permisos.show');
    Route::post('/permisos/{id}/aprobar', [SolicitudPermisoController::class, 'aprobar'])->name('permisos.aprobar');
    Route::post('/permisos/{id}/rechazar', [SolicitudPermisoController::class, 'rechazar'])->name('permisos.rechazar');
});

Route::middleware(['auth', 'role:administrador|recursos_humanos'])->group(function () {
    Route::get('/solicitudes_permisos', [SolicitudPermisoController::class, 'indexSolicitudesPermiso'])->name('solicitudes_permisos.index');
    Route::get('/solicitudes_permisos/export', [SolicitudPermisoController::class, 'export'])->name('solicitudes_permisos.export');
    Route::get('/solicitudes_permisos/exportWeek', [SolicitudPermisoController::class, 'exportWeek'])->name('solicitudes_permisos.exportWeek');
    Route::get('/solicitudes_permisos/download-zip', [SolicitudPermisoController::class, 'downloadSolicitudesZIP'])->name('solicitudes_permisos.download-zip');
    Route::get('/solicitudes_permisos/{id}', [SolicitudPermisoController::class, 'show'])->name('solicitudes_permisos.show');
    Route::get('/solicitudes_permisos/{id}/download', [SolicitudPermisoController::class, 'downloadPDF'])->name('solicitudes_permisos.download');

    Route::redirect('/rh/permisos', '/solicitudes_permisos')->name('rh.permisos.index');
});

/*
|--------------------------------------------------------------------------
| Vacaciones
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/vacaciones', [SolicitudVacacionController::class, 'index'])->name('vacaciones.index');
    Route::get('/vacaciones/create', [SolicitudVacacionController::class, 'create'])->name('vacaciones.create');
    Route::post('/vacaciones', [SolicitudVacacionController::class, 'store'])->name('vacaciones.store');
    Route::get('/vacaciones/{id}', [SolicitudVacacionController::class, 'show'])->name('vacaciones.show');

   
    Route::post('/vacaciones/{id}/aprobar', [SolicitudVacacionController::class, 'approve'])->name('vacaciones.aprobar');
    Route::post('/vacaciones/{id}/rechazar', [SolicitudVacacionController::class, 'reject'])->name('vacaciones.rechazar');

    Route::delete('/vacaciones/{vacacione}', [SolicitudVacacionController::class, 'destroy'])->name('vacaciones.destroy');
});

Route::delete('solicitudes-vacaciones/{id}/admin-destroy', [SolicitudVacacionController::class, 'destroyAdmin'])
    ->name('solicitudes_vacaciones.admin-destroy')
    ->middleware(['auth', 'role:administrador|recursos_humanos']);

// Vista de RH. Las rutas fijas van antes que las de {id}, si no, "export" se toma como ID.
Route::middleware(['auth', 'role:recursos_humanos'])->group(function () {
    Route::get('/solicitudes_vacaciones', [SolicitudVacacionController::class, 'indexRH'])->name('solicitudes_vacaciones.index');
    Route::get('/solicitudes_vacaciones/export', [SolicitudVacacionController::class, 'export'])->name('solicitudes_vacaciones.export');
    Route::get('/solicitudes_vacaciones/exportWeek', [SolicitudVacacionController::class, 'exportWeek'])->name('solicitudes_vacaciones.exportWeek');
    Route::get('/solicitudes_vacaciones/{id}', [SolicitudVacacionController::class, 'showRH'])->name('solicitudes_vacaciones.show');
    Route::get('/solicitudes_vacaciones/{id}/download', [SolicitudVacacionController::class, 'downloadPDF'])->name('solicitudes_vacaciones.download');
});

// Vacaciones espontáneas — registradas por RH en nombre del empleado
Route::middleware(['auth', 'role:administrador|recursos_humanos'])->group(function () {
    Route::get('/vacaciones-espontaneas/create', [SolicitudVacacionController::class, 'createEspontanea'])->name('vacaciones.espontanea.create');
    Route::post('/vacaciones-espontaneas', [SolicitudVacacionController::class, 'storeEspontanea'])->name('vacaciones.espontanea.store');
});

/*
|--------------------------------------------------------------------------
| Periodos de vacaciones
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:administrador|recursos_humanos'])->group(function () {
    Route::get('/periodos/create', [PeriodoVacacionController::class, 'create'])->name('periodos.create');
    Route::post('/periodos/store', [PeriodoVacacionController::class, 'store'])->name('periodos.store');
    Route::post('/calculate-days', [PeriodoVacacionController::class, 'calculateDays'])->name('calculate.days');
    Route::get('/periodos', [PeriodoVacacionController::class, 'index'])->name('periodos.index');
    Route::get('/periodos/export', [PeriodoVacacionController::class, 'export'])->name('periodos.export');
    Route::get('/periodos/{id}/editar', [PeriodoVacacionController::class, 'edit'])->name('periodos.edit');
    Route::put('/periodos/{id}', [PeriodoVacacionController::class, 'update'])->name('periodos.update');
    Route::delete('/periodos/{id}', [PeriodoVacacionController::class, 'destroy'])->name('periodos.destroy');
    Route::post('/periodos_vacaciones/{id}/toggle-activo', [PeriodoVacacionController::class, 'toggleActivo'])->name('periodos_vacaciones.toggle-activo');
});

/*
|--------------------------------------------------------------------------
| Noticias
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:administrador|marketing'])->group(function () {
    Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias.index');
    Route::patch('noticias/{noticia}/toggle', [NoticiaController::class, 'toggle'])->name('noticias.toggle');
    Route::get('/noticias/create', [NoticiaController::class, 'create'])->name('noticias.create');
    Route::post('/noticias', [NoticiaController::class, 'store'])->name('noticias.store');
    Route::get('/noticias/{noticia}/edit', [NoticiaController::class, 'edit'])->name('noticias.edit');
    Route::put('/noticias/{noticia}', [NoticiaController::class, 'update'])->name('noticias.update');
    Route::delete('/noticias/{noticia}', [NoticiaController::class, 'destroy'])->name('noticias.destroy');

    // TODO: el comentario original decía que esta ruta es pública, pero está protegida por rol.
    // Confirmar con marketing si debe ser pública antes de moverla fuera del grupo.
    Route::get('/noticias-publicas', [NoticiaController::class, 'publicIndex'])->name('noticias.public');

    Route::get('/noticias/{noticia}', function (Noticia $noticia) {
        return response()->json($noticia);
    });
});

/*
|--------------------------------------------------------------------------
| Carrusel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:administrador|marketing'])->group(function () {
    Route::resource('carrusel', CarruselController::class)->only(['index', 'create', 'store']);
    Route::delete('/carrusel/{id}', [CarruselController::class, 'destroy'])->name('carrusel.destroy');
    Route::put('/carrusel/{id}', [CarruselController::class, 'update'])->name('carrusel.update');
});

require __DIR__.'/auth.php';