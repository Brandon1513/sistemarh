<?php

/*
 * Plantillas de celebraciones. Las posiciones son en píxeles sobre el fondo de marketing,
 * a su tamaño original (y al final se achica a "salida_ancho").
 * "y" en los textos es la línea base (donde se apoyan las letras), no la parte de arriba.
 * Los textos con "ancho" se achican solos si no caben (útil para nombres largos).
 */

return [
    // Interruptor general de las celebraciones automáticas (cumpleaños, aniversarios y lista del mes).
// Apagado: los comandos de las 7:00 no publican nada. La invitación al festejo funciona igual.
'activas' => (bool) env('CELEBRACIONES_ACTIVAS', false),

    'colores' => [
        'morado' => '#6A2C75',
        'dorado' => '#C8892A',
        'cafe'   => '#473524',
        'rosa'   => '#AA4969',
        'blanco' => '#FFFFFF',
        'lila'   => '#BBA4C0',
        'gris'   => '#4A4A4A',
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
                [
                    'tipo' => 'parrafo',
                    'texto' => 'En este día tan especial te deseamos que todos tus sueños se cumplan y sea un año lleno de felicidad.',
                    'fuente' => 'serif',
                    'tamano' => 31,
                    'color' => 'morado',
                    'x' => 64,
                    'y' => 1800,
                    'ancho' => 1085,
                    'alineacion' => 'izquierda',
                    'interlineado' => 56
                ],
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
                [
                    'tipo' => 'parrafo',
                    'texto' => 'Hoy nos llena de orgullo celebrar un año más de tu colaboración y tenacidad, trabajando juntos por Dasavena',
                    'fuente' => 'texto',
                    'tamano' => 23,
                    'color' => 'blanco',
                    'y' => 735,
                    'ancho' => 690,
                    'interlineado' => 40
                ],
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
                [
                    'tipo' => 'lista',
                    'y' => 658,
                    'alto_fila' => 42,
                    'fuente' => 'serif',
                    'tamano' => 25,
                    'color' => 'morado',
                    'titulo_fuente' => 'serif',
                    'titulo_tamano' => 34,
                    'titulo_color' => 'dorado',
                    'antes_titulo' => 42,
                    'despues_titulo' => 64,
                    'columnas' => ['dia' => 41, 'nombre' => 88, 'puesto' => 867]
                ],
            ],
        ],

        // ── Invitación general, con confeti (fondo de 1545 × 1999) ─────────────
        'festejo_general' => [
            'fondo'        => 'celebraciones/fondo-festejo.png',
            'provisional'  => 'crema',
            'ancho'        => 1545,
            'alto'         => 1999,
            'salida_ancho' => 1080,
            'elementos'    => [
                ['tipo' => 'texto', 'texto' => 'Festejo', 'fuente' => 'manuscrita', 'tamano' => 130, 'ancho' => 520, 'color' => 'morado', 'y' => 590],
                ['tipo' => 'texto', 'texto' => 'cumpleaños', 'fuente' => 'manuscrita', 'tamano' => 130, 'ancho' => 800, 'color' => 'morado', 'y' => 740],
                ['tipo' => 'texto', 'texto' => '{MES}', 'fuente' => 'grande', 'tamano' => 135, 'ancho' => 500, 'color' => 'morado', 'y' => 940],
                [
                    'tipo' => 'parrafo',
                    'texto' => 'Te invitamos a festejar con los cumpleañeros del mes',
                    'fuente' => 'manuscrita',
                    'tamano' => 62,
                    'color' => 'gris',
                    'y' => 1135,
                    'ancho' => 830,
                    'interlineado' => 85
                ],
                ['tipo' => 'texto', 'texto' => '{DIA}', 'fuente' => 'negrita', 'tamano' => 56, 'color' => 'morado', 'y' => 1468],
                ['tipo' => 'texto', 'texto' => '{FECHA}', 'fuente' => 'negrita', 'tamano' => 56, 'ancho' => 1000, 'color' => 'morado', 'y' => 1540],
                ['tipo' => 'parrafo', 'texto' => '{HORA_LUGAR}', 'fuente' => 'negrita', 'tamano' => 56, 'color' => 'morado', 'y' => 1690, 'ancho' => 850, 'interlineado' => 85],
            ],
        ],

        // ── Invitación por turno, con su lista (fondo de 1545 × 1999) ──────────
        'festejo_turno' => [
            'fondo'        => 'celebraciones/fondo-lista-mes.png',
            'provisional'  => 'crema',
            'ancho'        => 1545,
            'alto'         => 1999,
            'salida_ancho' => 1080,
            'elementos'    => [
                ['tipo' => 'texto', 'texto' => 'Festejo cumpleaños', 'fuente' => 'manuscrita', 'tamano' => 120, 'ancho' => 1150, 'color' => 'morado', 'y' => 440],
                ['tipo' => 'texto', 'texto' => '{MES}', 'fuente' => 'grande', 'tamano' => 125, 'ancho' => 520, 'color' => 'morado', 'y' => 600],
                ['tipo' => 'texto', 'texto' => 'Queremos festejar sus cumpleaños el próximo', 'fuente' => 'texto', 'tamano' => 33, 'color' => 'morado', 'y' => 662],
                ['tipo' => 'texto', 'texto' => '{FECHA_LARGA}', 'fuente' => 'negrita', 'tamano' => 33, 'color' => 'morado', 'y' => 708],
                ['tipo' => 'texto', 'texto' => '{NOTA}', 'fuente' => 'texto', 'tamano' => 33, 'ancho' => 1300, 'color' => 'morado', 'y' => 756],
                ['tipo' => 'texto', 'texto' => '{HORA_LUGAR}', 'fuente' => 'negrita', 'tamano' => 40, 'ancho' => 1250, 'color' => 'morado', 'y' => 850],
                [
                    'tipo' => 'lista',
                    'y' => 848,
                    'alto_fila' => 47,
                    'fuente' => 'serif',
                    'tamano' => 26,
                    'color' => 'morado',
                    'titulo_fuente' => 'serif',
                    'titulo_tamano' => 34,
                    'titulo_color' => 'dorado',
                    'antes_titulo' => 42,
                    'despues_titulo' => 80,
                    'columnas' => ['dia' => 47, 'nombre' => 122, 'puesto' => 867]
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
