<?php

use App\Http\Controllers\CarruselController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\DiaInhabilController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\PeriodoVacacionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolicitudPermisoController;
use App\Http\Controllers\SolicitudVacacionController;
use App\Models\CarruselImage;
use App\Models\Noticia;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $noticias = \App\Models\Comunicado::visibles()->paraUsuario(auth()->user())->with('imagenes')->take(5)->get();
    $carruselImages = CarruselImage::all();

    return view('dashboard', compact('noticias', 'carruselImages'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Todos los comunicados ("Ver todas"). Cualquier empleado con sesión;
// cada quien ve solo los que son para él (lo filtra paraUsuario en el controlador)
Route::get('/noticias-publicas', [NoticiaController::class, 'publicIndex'])
    ->middleware('auth')
    ->name('noticias.public');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
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
    Route::get('/solicitudes-permisos/export', [SolicitudPermisoController::class, 'export'])->name('solicitudes_permisos.export');
    Route::get('/solicitudes-permisos/exportWeek', [SolicitudPermisoController::class, 'exportWeek'])->name('solicitudes_permisos.exportWeek');
    Route::get('/solicitudes-permisos/download-zip', [SolicitudPermisoController::class, 'downloadSolicitudesZIP'])->name('solicitudes_permisos.download-zip');
    Route::get('/solicitudes-permisos/{id}', [SolicitudPermisoController::class, 'show'])->name('solicitudes_permisos.show');
    Route::get('/solicitudes-permisos/{id}/download', [SolicitudPermisoController::class, 'downloadPDF'])->name('solicitudes_permisos.download');

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
// Las URLs con más de un segmento usan guiones: en DomCloud, el guion bajo en esas rutas da 404.
Route::middleware(['auth', 'role:recursos_humanos'])->group(function () {
    Route::get('/solicitudes_vacaciones', [SolicitudVacacionController::class, 'indexRH'])->name('solicitudes_vacaciones.index');
    Route::get('/solicitudes-vacaciones/export', [SolicitudVacacionController::class, 'export'])->name('solicitudes_vacaciones.export');
    Route::get('/solicitudes-vacaciones/exportWeek', [SolicitudVacacionController::class, 'exportWeek'])->name('solicitudes_vacaciones.exportWeek');
    Route::get('/solicitudes-vacaciones/{id}', [SolicitudVacacionController::class, 'showRH'])->name('solicitudes_vacaciones.show');
    Route::get('/solicitudes-vacaciones/{id}/download', [SolicitudVacacionController::class, 'downloadPDF'])->name('solicitudes_vacaciones.download');
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
    Route::post('/periodos-vacaciones/{id}/toggle-activo', [PeriodoVacacionController::class, 'toggleActivo'])->name('periodos_vacaciones.toggle-activo');
});

/*
|--------------------------------------------------------------------------
| Noticias (panel viejo de marketing; se retira cuando los comunicados estén en producción)
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

/*
|--------------------------------------------------------------------------
| Comunicados: panel de marketing y RH
|--------------------------------------------------------------------------
| Los permisos por tipo los revisa ComunicadoPolicy en el controlador.
*/

Route::middleware('auth')
    ->prefix('gestion/comunicados')
    ->name('comunicados.admin.')
    ->controller(ComunicadoController::class)
    ->group(function () {
        Route::get('/buscar-personas', 'buscarPersonas')->name('buscar-personas');
        Route::post('/contar-destinatarios', 'contarDestinatarios')->name('contar-destinatarios');

        Route::get('/', 'index')->name('index');
        Route::get('/nuevo', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{comunicado}/editar', 'edit')->name('edit');
        Route::put('/{comunicado}', 'update')->name('update');
        Route::post('/{comunicado}/publicar', 'publicar')->name('publicar');
        Route::post('/{comunicado}/archivar', 'archivar')->name('archivar');
        Route::delete('/{comunicado}', 'destroy')->name('destroy');
        Route::get('/{comunicado}/revisar', 'revisar')->name('revisar');
        Route::post('/{comunicado}/aprobar', 'aprobar')->name('aprobar');
        Route::post('/{comunicado}/devolver', 'devolver')->name('devolver');
        Route::post('/{comunicado}/retirar', 'retirar')->name('retirar');
    });

    /*
    |--------------------------------------------------------------------------
    | Grupos para dirigir comunicados (los administra RH)
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth', 'role:administrador|recursos_humanos'])
        ->prefix('gestion/grupos')
        ->name('grupos.')
        ->controller(GrupoController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/nuevo', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{grupo}/editar', 'edit')->name('edit');
            Route::put('/{grupo}', 'update')->name('update');
            Route::patch('/{grupo}/toggle', 'toggle')->name('toggle');
            Route::delete('/{grupo}', 'destroy')->name('destroy');
        });

/*
|--------------------------------------------------------------------------
| Días inhábiles por sede (los administra RH)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:administrador|recursos_humanos'])->group(function () {
    Route::get('/dias-inhabiles', [DiaInhabilController::class, 'index'])->name('dias_inhabiles.index');
    Route::get('/dias-inhabiles/crear', [DiaInhabilController::class, 'create'])->name('dias_inhabiles.create');
    Route::post('/dias-inhabiles', [DiaInhabilController::class, 'store'])->name('dias_inhabiles.store');
    Route::get('/dias-inhabiles/{diaInhabil}/editar', [DiaInhabilController::class, 'edit'])->name('dias_inhabiles.edit');
    Route::put('/dias-inhabiles/{diaInhabil}', [DiaInhabilController::class, 'update'])->name('dias_inhabiles.update');
    Route::delete('/dias-inhabiles/{diaInhabil}', [DiaInhabilController::class, 'destroy'])->name('dias_inhabiles.destroy');
});

require __DIR__.'/auth.php';