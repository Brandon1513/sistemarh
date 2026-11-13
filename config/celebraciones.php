<?php

/*
 * Plantillas de celebraciones. Las posiciones son en píxeles sobre el fondo de marketing,
 * a su tamaño original (y al final se achica a "salida_ancho").
 * "y" en los textos es la línea base (donde se apoyan las letras), no la parte de arriba.
 * Los textos con "ancho" se achican solos si no caben (útil para nombres largos).
 */

return [

    'colores' => [
        'morado' => '#6A2C75',
        'dorado' => '#C8892A',
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
        // La de los textos corridos y la lista (pregúntale a marketing el nombre exacto)
        'serif'      => 'fonts/Lora-Regular.ttf',
    ],

    'plantillas' => [

        // ── ¡Feliz cumpleaños, Brandon! (fondo de 1545 × 1999) ─────────────
        'cumpleanos' => [
            'fondo'        => 'celebraciones/fondo-cumpleanos.png',
            'provisional'  => 'crema',
            'ancho'        => 1545,
            'alto'         => 1999,
            'salida_ancho' => 1080,
            'elementos'    => [
                ['tipo' => 'texto', 'texto' => '¡Feliz Cumpleaños', 'fuente' => 'manuscrita', 'tamano' => 100, 'ancho' => 1110, 'color' => 'morado', 'y' => 610],
                ['tipo' => 'texto', 'texto' => '{nombre}!', 'fuente' => 'manuscrita', 'tamano' => 100, 'ancho' => 1000, 'color' => 'morado', 'y' => 735],
                ['tipo' => 'foto', 'x' => 437, 'y' => 821, 'ancho' => 670, 'alto' => 899, 'forma' => 'rectangulo', 'borde' => 'morado', 'grosor' => 11],
                ['tipo' => 'parrafo', 'texto' => 'En este día tan especial te deseamos que todos tus sueños se cumplan y sea un año lleno de felicidad.',
                    'fuente' => 'serif', 'tamano' => 31, 'color' => 'morado', 'x' => 64, 'y' => 1800, 'ancho' => 1085, 'alineacion' => 'izquierda', 'interlineado' => 56],
                ['tipo' => 'texto', 'texto' => 'Con cariño, Equipo Dasavena', 'fuente' => 'serif', 'tamano' => 31, 'color' => 'morado', 'x' => 64, 'y' => 1912, 'alineacion' => 'izquierda'],
            ],
        ],

        // ── ¡Felicidades! 4 años (fondo de 1080 × 1920) ────────────────────
        'aniversario' => [
            'fondo'        => 'celebraciones/fondo-aniversario.png',
            'provisional'  => 'morado',
            'ancho'        => 1080,
            'alto'         => 1920,
            'elementos'    => [
                ['tipo' => 'texto', 'texto' => '¡Felicidades!', 'fuente' => 'manuscrita', 'tamano' => 120, 'ancho' => 812, 'color' => 'blanco', 'y' => 462],
                ['tipo' => 'texto', 'texto' => '{NOMBRE}', 'fuente' => 'grande', 'tamano' => 48, 'ancho' => 680, 'color' => 'blanco', 'y' => 612],
                ['tipo' => 'parrafo', 'texto' => 'Hoy nos llena de orgullo celebrar un año más de tu colaboración y tenacidad, trabajando juntos por Dasavena',
                    'fuente' => 'texto', 'tamano' => 23, 'color' => 'blanco', 'y' => 735, 'ancho' => 690, 'interlineado' => 40],
                ['tipo' => 'texto', 'texto' => '{ANIOS}', 'fuente' => 'grande', 'tamano' => 150, 'ancho' => 620, 'color' => 'blanco', 'y' => 1045],
                ['tipo' => 'foto', 'x' => 272, 'y' => 1062, 'ancho' => 536, 'alto' => 536, 'forma' => 'circulo', 'borde' => 'lila', 'grosor' => 3],
                ['tipo' => 'texto', 'texto' => '{FECHA_INGRESO}', 'fuente' => 'grande', 'tamano' => 50, 'color' => 'blanco', 'y' => 1672],
                ['tipo' => 'texto', 'texto' => '¡GRACIAS POR SER PARTE', 'fuente' => 'grande', 'tamano' => 51, 'color' => 'morado', 'y' => 1790],
                ['tipo' => 'texto', 'texto' => 'DE NUESTRA HISTORIA!', 'fuente' => 'grande', 'tamano' => 51, 'color' => 'morado', 'y' => 1862],
            ],
        ],

        // ── Cumpleañeros del mes (fondo de 1545 × 1999) ─────────────────────
        'lista_mes' => [
            'fondo'        => 'celebraciones/fondo-lista-mes.png',
            'provisional'  => 'crema',
            'ancho'        => 1545,
            'alto'         => 1999, // si la lista es más larga, la imagen crece sola
            'salida_ancho' => 1080,
            'elementos'    => [
                ['tipo' => 'texto', 'texto' => 'Cumpleañeros', 'fuente' => 'manuscrita', 'tamano' => 140, 'ancho' => 1080, 'color' => 'morado', 'y' => 465],
                ['tipo' => 'texto', 'texto' => '{MES}', 'fuente' => 'grande', 'tamano' => 135, 'ancho' => 700, 'color' => 'morado', 'y' => 615],
                ['tipo' => 'lista', 'y' => 658, 'alto_fila' => 42, 'fuente' => 'serif', 'tamano' => 25, 'color' => 'morado',
                    'titulo_fuente' => 'serif', 'titulo_tamano' => 34, 'titulo_color' => 'dorado',
                    'antes_titulo' => 42, 'despues_titulo' => 64,
                    'columnas' => ['dia' => 41, 'nombre' => 88, 'puesto' => 867]],
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