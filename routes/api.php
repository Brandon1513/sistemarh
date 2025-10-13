<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

