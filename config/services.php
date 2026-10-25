<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'microsoft_graph' => [
    'tenant_id'     => env('MS_GRAPH_TENANT_ID'),
    'client_id'     => env('MS_GRAPH_CLIENT_ID'),
    'client_secret' => env('MS_GRAPH_CLIENT_SECRET'),
    'remitente'     => env('MS_GRAPH_REMITENTE'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'azure' => [
    'client_id'     => env('MS_LOGIN_CLIENT_ID'),
    'client_secret' => env('MS_LOGIN_CLIENT_SECRET'),
    'redirect'      => env('MS_LOGIN_REDIRECT'),
    'tenant'        => env('MS_GRAPH_TENANT_ID'),
    'proxy'         => env('PROXY'),
    // Dominios cuyas cuentas de Microsoft pueden entrar (separados por coma)
    'dominios'      => array_filter(array_map('trim', explode(',', env('MS_LOGIN_DOMINIOS', 'dasavena.com')))),
    ],

];
