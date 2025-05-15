<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Messaging;

class NotificationController extends Controller
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'userId' => 'required|integer',
            'title'  => 'required|string',
            'body'   => 'required|string',
        ]);

        $user = \App\Models\User::find($request->userId);

        if (!$user || !$user->fcm_token) {
            return response()->json(['message' => 'Usuario o token no encontrado.'], 404);
        }

        $message = [
            'token' => $user->fcm_token,
            'notification' => [
                'title' => $request->title,
                'body'  => $request->body,
            ],
            'android' => [
                'priority' => 'high',
            ],
        ];

        try {
            $this->messaging->send($message);
            return response()->json(['message' => 'Notificación enviada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
