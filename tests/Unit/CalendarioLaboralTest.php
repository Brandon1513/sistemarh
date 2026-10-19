<?php

use App\Support\CalendarioLaboral;
use Carbon\Carbon;

test('calcula los festivos que cambian de fecha cada año', function () {
    $f2026 = CalendarioLaboral::festivos(2026);

    expect($f2026)->toHaveKeys([
        '2026-02-02', // primer lunes de febrero
        '2026-03-16', // tercer lunes de marzo
        '2026-04-03', // Viernes Santo (Pascua: 5 de abril)
        '2026-04-04', // Sábado Santo
        '2026-11-16', // tercer lunes de noviembre
        '2026-12-24', '2026-12-25', '2026-12-31',
    ]);

    // 2025 coincide con la lista que estaba escrita a mano en el controlador
    expect(array_keys(CalendarioLaboral::festivos(2025)))->toEqual([
        '2025-01-01', '2025-02-03', '2025-03-17', '2025-04-18', '2025-04-19',
        '2025-05-01', '2025-09-16', '2025-10-12', '2025-11-17', '2025-12-24', '2025-12-25', '2025-12-31',
    ]);
});

test('incluye el 1 de octubre solo en años de cambio de gobierno', function () {
    expect(CalendarioLaboral::festivos(2030))->toHaveKey('2030-10-01');
    expect(CalendarioLaboral::festivos(2026))->not->toHaveKey('2026-10-01');
});

test('no cuenta domingos ni festivos', function () {
    // Sáb 19 → Lun 21 dic 2026: el domingo 20 no cuenta
    expect(CalendarioLaboral::diasHabiles('2026-12-19', '2026-12-21'))->toBe(2);

    // Lun 21 → Sáb 26 dic 2026: se excluyen 24 y 25
    expect(CalendarioLaboral::diasHabiles('2026-12-21', '2026-12-26'))->toBe(4);
});

test('calcula la fecha de término para un número de días', function () {
    // 2 días desde el sábado 19 dic 2026 → termina el lunes 21
    expect(CalendarioLaboral::fechaTermino('2026-12-19', 2)->toDateString())->toBe('2026-12-21');

    // 5 días desde el lunes 21 dic 2026 → salta 24, 25 y domingo 27 → lunes 28
    expect(CalendarioLaboral::fechaTermino('2026-12-21', 5)->toDateString())->toBe('2026-12-28');

    // 1 día → termina el mismo día
    expect(CalendarioLaboral::fechaTermino('2026-10-20', 1)->toDateString())->toBe('2026-10-20');
});

test('la fecha de término siempre cubre exactamente los días pedidos', function () {
    $inicio = Carbon::parse('2026-01-02');
    foreach (range(1, 30) as $dias) {
        $fin = CalendarioLaboral::fechaTermino($inicio, $dias);
        expect(CalendarioLaboral::diasHabiles($inicio, $fin))->toBe($dias);
        expect(CalendarioLaboral::esHabil($fin))->toBeTrue();
    }
});

test('la reincorporación es el siguiente día hábil', function () {
    // Termina miércoles 23 dic 2026 → 24 y 25 son festivos → sábado 26
    expect(CalendarioLaboral::siguienteHabil('2026-12-23')->toDateString())->toBe('2026-12-26');

    // Termina sábado 19 dic 2026 → domingo 20 no → lunes 21
    expect(CalendarioLaboral::siguienteHabil('2026-12-19')->toDateString())->toBe('2026-12-21');
});
