<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Messaging;
use App\Models\UserDeviceToken;

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

        $tokens = UserDeviceToken::where('user_id', $request->userId)->pluck('token');

        if ($tokens->isEmpty()) {
            return response()->json(['message' => 'No se encontraron tokens para este usuario.'], 404);
        }

        $messages = $tokens->map(function ($token) use ($request) {
            return [
                'token' => $token,
                'notification' => [
                    'title' => $request->title,
                    'body'  => $request->body,
                ],
                'android' => [
                    'priority' => 'high',
                ],
            ];
        })->toArray();

        try {
            foreach ($messages as $message) {
                $this->messaging->send($message);
            }

            return response()->json(['message' => 'Notificaciones enviadas correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
