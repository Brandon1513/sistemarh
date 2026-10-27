<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Contract\Messaging;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\V2\AssetController;

use App\Http\Controllers\Api\V2\BrandController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserDeviceTokenController;
use App\Http\Controllers\Api\V2\AssignmentController;
use App\Http\Controllers\Api\V2\UserAssetsController;
use App\Http\Controllers\Api\V2\PublicAssetController;



/*
|--------------------------------------------------------------------------
| API v1 — App móvil nueva
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\V1\AuthController as AuthV1Controller;
use App\Http\Controllers\Api\V1\DispositivoController;
use App\Http\Controllers\Api\V1\VacacionController;
use App\Http\Controllers\Api\V1\PaseController;
use App\Http\Controllers\Api\V1\PermisoController;
use App\Http\Controllers\Api\V1\SolicitudController;
use App\Http\Controllers\Api\V1\AprobacionController;
use App\Http\Controllers\Api\V1\ComunicadoController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------

*/

/**
 * Ruta de prueba para verificar que el API está funcionando
 * Puedes probar accediendo a /api/ping
 */
Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando correctamente']);
});

/**
 * Ruta para obtener la foto de perfil de un usuario por ID
 * Protegida con auth:sanctum
 */
Route::middleware(['stateful', 'auth:sanctum'])->get('/user/{id}/foto-perfil', [UserController::class, 'fotoPerfil']);


//users
Route::post('/login', [AuthController::class, 'login']);

//notifications

Route::post('/notifications/register-token', function (Request $request, Messaging $messaging) {
    $request->validate([
        'token' => 'required|string',
        'userId' => 'required|integer',
    ]);

    $userId = $request->userId;
    $fcmToken = $request->token;

    // Aquí puedes guardar el token en la tabla users o una tabla separada
    \App\Models\User::where('id', $userId)->update(['fcm_token' => $fcmToken]);

    return response()->json(['message' => 'Token registrado correctamente.']);
});



Route::post('/notifications/send', [NotificationController::class, 'sendNotification']);

Route::post('/save-device-token', [UserDeviceTokenController::class, 'store']);



/*
|--------------------------------------------------------------------------
| API Routes SISTEMAS V2
|--------------------------------------------------------------------------

*/

Route::middleware(['auth:sanctum'])->group(function () {
  // Activos
  Route::get('/assets', [AssetController::class,'index']);
  Route::post('/assets', [AssetController::class,'store']);
  Route::get('/assets/{asset}', [AssetController::class,'show']);
  Route::put('/assets/{asset}', [AssetController::class,'update']);
  

  // Asignaciones
  Route::post('/assignments', [AssignmentController::class,'assign']);
  Route::post('/assignments/{assignment}/return', [AssignmentController::class,'return']);
  Route::get('/assignments', [AssignmentController::class,'index']); // ← nuevo
  Route::get('/brands', [BrandController::class,'index']);      // listado para el select
  Route::post('/brands', [BrandController::class,'store']);     // opcional: crear desde UI
  // Activos actuales por usuario
  Route::get('/users/{user}/assets', [UserAssetsController::class,'index']);
});

//ruta para el qr vista publica
  Route::get('public/assets/{id}', [PublicAssetController::class, 'show'])
     ->withoutMiddleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/asset-types', function () {
    return \App\Models\AssetType::orderBy('name')->get();
});


Route::middleware('auth:sanctum')->get('/me', function (Request $r) {
    return $r->user();
});

Route::get('/users', function (Request $r) {
    $q      = $r->query('search');
    $per    = (int) $r->query('per_page', 20);   // tamaño de página
    $users  = User::query()
        ->when($q, fn($qq) => $qq->where(function($w) use ($q) {
            $w->where('name','like',"%{$q}%")
              ->orWhere('email','like',"%{$q}%");
        }))
        ->orderBy('name')
        ->paginate($per, ['id','name','email']); // ← paginate!

    return response()->json($users);
});


/*
|--------------------------------------------------------------------------
| API v1 — App móvil nueva
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Rutas públicas para entrar, con límite de intentos por minuto contra ataques de fuerza bruta
    Route::post('/login', [AuthV1Controller::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/microsoft', [AuthV1Controller::class, 'microsoft'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthV1Controller::class, 'logout']);
        Route::get('/me', [AuthV1Controller::class, 'me']);
        Route::post('/dispositivos', [DispositivoController::class, 'store']);

        // Vacaciones
        Route::get('/dias-festivos', [VacacionController::class, 'festivos']);
        Route::get('/vacaciones/periodos', [VacacionController::class, 'periodos']);
        Route::post('/vacaciones', [VacacionController::class, 'store']);
        Route::delete('/vacaciones/{solicitud}', [VacacionController::class, 'destroy']);

        // Solicitudes del empleado
        Route::get('/solicitudes', [SolicitudController::class, 'index']);
        Route::get('/catalogos', [SolicitudController::class, 'catalogos']);

        Route::post('/permisos', [PermisoController::class, 'store']);
        Route::delete('/permisos/{permiso}', [PermisoController::class, 'destroy']);

        Route::post('/pases', [PaseController::class, 'store']);
        Route::delete('/pases/{pase}', [PaseController::class, 'destroy']);

        // Bandeja de aprobaciones del jefe
        Route::get('/aprobaciones', [AprobacionController::class, 'index']);
        Route::post('/aprobaciones/{tipo}/{id}/aprobar', [AprobacionController::class, 'aprobar'])
            ->whereIn('tipo', ['vacaciones', 'permiso', 'pase'])->whereNumber('id');
        Route::post('/aprobaciones/{tipo}/{id}/rechazar', [AprobacionController::class, 'rechazar'])
            ->whereIn('tipo', ['vacaciones', 'permiso', 'pase'])->whereNumber('id');

            // Comunicados para los empleados
        Route::get('/comunicados', [ComunicadoController::class, 'index']);
        Route::get('/comunicados/{comunicado}', [ComunicadoController::class, 'show'])->whereNumber('comunicado');
    });
});