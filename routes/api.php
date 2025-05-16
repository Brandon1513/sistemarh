<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\UserDeviceTokenController;
use Kreait\Firebase\Contract\Messaging;
use App\Http\Controllers\NotificationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar todas las rutas API de tu aplicación. Estas rutas
| son automáticamente prefijadas con /api y cargadas a través del archivo
| bootstrap/app.php, si has definido el registro correctamente.
|
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
