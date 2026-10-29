<?php

return [
    // El grupo de Microsoft 365 de toda la empresa. Si un comunicado es "para todos", se manda aquí.
    'correo_todos' => env('COMUNICADOS_CORREO_TODOS'),

    // Desde qué buzón sale cada tipo. Los dos tienen que estar en la política de acceso de Graph.
    'remitentes' => [
        'aviso' => [
            'correo' => env('COMUNICADOS_REMITENTE_RH', 'comunicacion@dasavena.com'),
            'nombre' => 'Recursos Humanos Dasavena',
        ],
        'otros' => [
            'correo' => env('COMUNICADOS_REMITENTE_INTRANET', 'intranet@dasavena.com'),
            'nombre' => 'Intranet Dasavena',
        ],
    ],

    // Solo se manda a correos de estos dominios (los embajadores sin correo de Dasavena se enteran por la app)
    'dominios' => array_values(array_filter(array_map('trim', explode(',', (string) env('MS_LOGIN_DOMINIOS', 'dasavena.com'))))),
];