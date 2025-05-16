<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserDeviceToken;

class UserDeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'token'   => 'required|string',
        ]);

        UserDeviceToken::updateOrCreate(
            ['user_id' => $request->user_id, 'token' => $request->token],
            []
        );

        return response()->json(['message' => 'Token registrado correctamente.']);
    }
}
