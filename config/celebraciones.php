<?php

/*
 * Plantillas de celebraciones. Las posiciones son en píxeles sobre una imagen de 1080 de ancho.
 * "y" en los textos es la línea base (donde se apoyan las letras), no la parte de arriba.
 * Si el archivo del "fondo" no existe todavía, se dibuja uno provisional.
 */

return [

    'colores' => [
        'morado' => '#6A2C75',
        'dorado' => '#C9952F',
        'cafe'   => '#473524',
        'rosa'   => '#AA4969',
        'blanco' => '#FFFFFF',
        'lila'   => '#BBA4C0',
    ],

    'fuentes' => [
        'manuscrita' => 'fonts/ThirstyRoughRegTwo.otf',
        'grande'     => 'fonts/Veneer.ttf',
        'texto'      => 'fonts/Poppins-Regular.ttf',
        'negrita'    => 'fonts/Poppins-SemiBold.ttf',
    ],

    'plantillas' => [

        // ── ¡Feliz cumpleaños, Jocelin! ───────────────────────────
        'cumpleanos' => [
            'fondo'       => 'celebraciones/fondo-cumpleanos.png',
            'provisional' => 'crema',
            'ancho'       => 1080,
            'alto'        => 1400,
            'elementos'   => [
                ['tipo' => 'texto', 'texto' => '¡Feliz Cumpleaños', 'fuente' => 'manuscrita', 'tamano' => 72, 'color' => 'morado', 'y' => 400],
                ['tipo' => 'texto', 'texto' => '{nombre}!', 'fuente' => 'manuscrita', 'tamano' => 72, 'color' => 'morado', 'y' => 500],
                ['tipo' => 'foto', 'x' => 280, 'y' => 560, 'ancho' => 520, 'alto' => 600, 'forma' => 'rectangulo', 'borde' => 'morado', 'grosor' => 10],
                [
                    'tipo' => 'parrafo',
                    'texto' => 'En este día tan especial te deseamos que todos tus sueños se cumplan y sea un año lleno de felicidad.',
                    'fuente' => 'texto',
                    'tamano' => 24,
                    'color' => 'morado',
                    'x' => 50,
                    'y' => 1235,
                    'ancho' => 760,
                    'alineacion' => 'izquierda'
                ],
                ['tipo' => 'texto', 'texto' => 'Con cariño, Equipo Dasavena', 'fuente' => 'texto', 'tamano' => 24, 'color' => 'morado', 'x' => 50, 'y' => 1355, 'alineacion' => 'izquierda'],
            ],
        ],

        // ── ¡Felicidades! 6 años en Dasavena ──────────────────────
        'aniversario' => [
            'fondo'       => 'celebraciones/fondo-aniversario.png',
            'provisional' => 'morado',
            'ancho'       => 1080,
            'alto'        => 1900,
            'elementos'   => [
                ['tipo' => 'texto', 'texto' => '¡Felicidades!', 'fuente' => 'manuscrita', 'tamano' => 96, 'color' => 'blanco', 'y' => 470],
                ['tipo' => 'texto', 'texto' => '{NOMBRE}', 'fuente' => 'grande', 'tamano' => 50, 'color' => 'blanco', 'y' => 570],
                ['tipo' => 'texto', 'texto' => '{PUESTO}', 'fuente' => 'grande', 'tamano' => 40, 'color' => 'blanco', 'y' => 640],
                [
                    'tipo' => 'parrafo',
                    'texto' => 'Hoy nos llena de orgullo celebrar un año más de tu colaboración y tenacidad, trabajando juntos por Dasavena',
                    'fuente' => 'texto',
                    'tamano' => 24,
                    'color' => 'blanco',
                    'y' => 720,
                    'ancho' => 760
                ],
                ['tipo' => 'texto', 'texto' => '{ANIOS}', 'fuente' => 'grande', 'tamano' => 150, 'color' => 'blanco', 'y' => 1040],
                ['tipo' => 'foto', 'x' => 290, 'y' => 1080, 'ancho' => 500, 'alto' => 500, 'forma' => 'circulo', 'borde' => 'blanco', 'grosor' => 10],
                ['tipo' => 'texto', 'texto' => '{FECHA_INGRESO}', 'fuente' => 'grande', 'tamano' => 44, 'color' => 'blanco', 'y' => 1660],
                ['tipo' => 'texto', 'texto' => '¡GRACIAS POR SER PARTE', 'fuente' => 'grande', 'tamano' => 50, 'color' => 'morado', 'y' => 1790],
                ['tipo' => 'texto', 'texto' => 'DE NUESTRA HISTORIA!', 'fuente' => 'grande', 'tamano' => 50, 'color' => 'morado', 'y' => 1855],
            ],
        ],

        // ── Cumpleañeros del mes ──────────────────────────────────
        'lista_mes' => [
            'fondo'       => 'celebraciones/fondo-lista-mes.png',
            'provisional' => 'crema',
            'ancho'       => 1080,
            'alto'        => 1350, // si la lista es más larga, la imagen crece sola
            'elementos'   => [
                ['tipo' => 'texto', 'texto' => 'Cumpleañeros', 'fuente' => 'manuscrita', 'tamano' => 110, 'color' => 'morado', 'y' => 450],
                ['tipo' => 'texto', 'texto' => '{MES}', 'fuente' => 'grande', 'tamano' => 110, 'color' => 'morado', 'y' => 580],
                [
                    'tipo' => 'lista',
                    'y' => 660,
                    'alto_fila' => 34,
                    'fuente' => 'texto',
                    'tamano' => 17,
                    'color' => 'morado',
                    'titulo_fuente' => 'negrita',
                    'titulo_tamano' => 24,
                    'titulo_color' => 'dorado',
                    'columnas' => ['dia' => 60, 'nombre' => 110, 'puesto' => 600]
                ],
            ],
        ],
    ],
    /*
 * Cómo se avisa de cada celebración, además de publicarla en la web y la app.
 *  - correo: un solo correo al grupo de toda la empresa (COMUNICADOS_CORREO_TODOS)
 *  - push_a_todos: push a todos los que tienen la app (quien cumple recibe además su felicitación personal)
 */
    'avisos' => [
        'cumpleanos'  => ['correo' => true, 'push_a_todos' => true],
        'aniversario' => ['correo' => true, 'push_a_todos' => true],
        'lista_mes'   => ['correo' => true, 'push_a_todos' => true],
    ],
];
