<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDeviceToken;

class UserDeviceTokenController extends Controller
{
    public function registerToken(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'token' => 'required|string|unique:user_device_tokens,token',
        ]);

        UserDeviceToken::updateOrCreate(
            ['user_id' => $request->userId],
            ['token' => $request->token]
        );

        return response()->json(['message' => 'Token registrado correctamente'], 200);
    }
}
