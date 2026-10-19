<?php

namespace App\Exports\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Reporte de Excel con la imagen corporativa de Dasavena:
 * logo, título, encabezados en morado, filas alternadas, estados con color,
 * filtros, fila de totales y configuración de impresión.
 *
 * Cada reporte define sus columnas en columns():
 *   'Encabezado' => ['value' => fn ($row) => ..., 'width' => 18, 'type' => 'text|date|time|number|status|long']
 */
abstract class BrandedExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents, WithDrawings, WithTitle, WithColumnFormatting, WithStrictNullComparison
{
    protected const HEADER_ROW = 6;

    protected const BRAND      = '6A2C75';
    protected const BRAND_SOFT = 'F3EEF5';
    protected const GOLD       = 'D6A644';
    protected const TEXT       = '2C1A30';
    protected const MUTED      = '7A6682';
    protected const BORDER     = 'E6D9ED';
    protected const ZEBRA      = 'FAF7FB';

    protected const STATUS_STYLES = [
        'Aprobado'  => ['fill' => 'E3F2E8', 'font' => '1F6B3F'],
        'Rechazado' => ['fill' => 'FBE9E9', 'font' => 'A52A2A'],
        'Pendiente' => ['fill' => 'FDF3D7', 'font' => '8A6100'],
        'Activo'    => ['fill' => 'E3F2E8', 'font' => '1F6B3F'],
        'Inactivo'  => ['fill' => 'EEEEEE', 'font' => '666666'],
    ];

    protected ?Collection $rowsCache = null;

    public function __construct(protected $records = null, protected ?string $subtitle = null)
    {
    }

    /** Título grande del reporte. */
    abstract protected function reportTitle(): string;

    /** Definición de columnas, en orden. */
    abstract protected function columns(): array;

    /** Registros a exportar; por defecto los que se pasan al constructor. */
    protected function records(): Collection
    {
        return collect($this->records);
    }

    public function title(): string
    {
        // Excel no admite estos caracteres en el nombre de la hoja y lo limita a 31
        $name = str_replace(['/', '\\', '?', '*', '[', ']', ':'], '-', $this->sheetTitle());

        return mb_substr($name, 0, 31);
    }

    /** Nombre corto de la pestaña de Excel. */
    protected function sheetTitle(): string
    {
        return $this->reportTitle();
    }

    public function startCell(): string
    {
        return 'A' . self::HEADER_ROW;
    }

    public function headings(): array
    {
        return array_keys($this->columns());
    }

    public function collection()
    {
        return $this->rows();
    }

    protected function rows(): Collection
    {
        if ($this->rowsCache !== null) {
            return $this->rowsCache;
        }

        $columns = $this->columns();

        return $this->rowsCache = $this->records()->map(function ($record) use ($columns) {
            return array_map(function ($column) use ($record) {
                $value = ($column['value'])($record);

                return match ($column['type'] ?? 'text') {
                    'date'   => $this->toExcelDate($value),
                    'time'   => $this->toExcelTime($value),
                    'status' => $this->statusLabel($value),
                    default  => $value === null || $value === '' ? null : $value,
                };
            }, array_values($columns));
        })->values();
    }

    public function columnFormats(): array
    {
        $formats = [];
        foreach (array_values($this->columns()) as $i => $column) {
            $format = match ($column['type'] ?? 'text') {
                'date'   => 'dd/mm/yyyy',
                'time'   => 'hh:mm AM/PM',
                'number' => '#,##0',
                default  => null,
            };
            if ($format) {
                $formats[Coordinate::stringFromColumnIndex($i + 1)] = $format;
            }
        }

        return $formats;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Dasavena');
        $drawing->setDescription('Logotipo Dasavena');
        $drawing->setPath(public_path('images/logo-pdf.png'));
        $drawing->setHeight(78);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(8);
        $drawing->setOffsetY(6);

        return $drawing;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $this->decorate($event->sheet->getDelegate());
            },
        ];
    }

    protected function decorate(Worksheet $sheet): void
    {
        $columns   = array_values($this->columns());
        $colCount  = count($columns);
        $lastCol   = Coordinate::stringFromColumnIndex($colCount);
        $headerRow = self::HEADER_ROW;
        $firstData = $headerRow + 1;
        $rowCount  = $this->rows()->count();
        $lastData  = $headerRow + max($rowCount, 1);

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
        $sheet->setShowGridlines(false);

        // ── Encabezado con logo y título ─────────────────────────────
        $sheet->getRowDimension(1)->setRowHeight(18);
        $sheet->getRowDimension(2)->setRowHeight(26);
        $sheet->getRowDimension(3)->setRowHeight(17);
        $sheet->getRowDimension(4)->setRowHeight(17);
        $sheet->getRowDimension(5)->setRowHeight(8);

        $sheet->setCellValue('B1', 'DASAVENA · RECURSOS HUMANOS');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(9)->getColor()->setRGB(self::GOLD);

        $sheet->setCellValue('B2', $this->reportTitle());
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB(self::BRAND);

        $sheet->setCellValue('B3', $this->subtitle ?: 'Todos los registros');
        $sheet->getStyle('B3')->getFont()->setSize(10)->getColor()->setRGB(self::TEXT);

        $generated = now()->timezone('America/Mexico_City')->locale('es');
        $sheet->setCellValue('B4', sprintf(
            'Generado el %s · %d %s',
            $generated->translatedFormat('d \d\e F \d\e Y, H:i') . ' h',
            $rowCount,
            $rowCount === 1 ? 'registro' : 'registros'
        ));
        $sheet->getStyle('B4')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB(self::MUTED);

        foreach (['B1', 'B2', 'B3', 'B4'] as $cell) {
            $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        $sheet->getStyle("A5:{$lastCol}5")->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::GOLD);

        // ── Anchos de columna ────────────────────────────────────────
        foreach ($columns as $i => $column) {
            $letter = Coordinate::stringFromColumnIndex($i + 1);
            $width  = $column['width'] ?? 16;
            // La columna A también aloja el logo
            $sheet->getColumnDimension($letter)->setWidth($i === 0 ? max($width, 13) : $width);
        }

        // ── Encabezados de la tabla ──────────────────────────────────
        $sheet->getRowDimension($headerRow)->setRowHeight(32);
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BRAND]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '541F5C']]],
        ]);

        if ($rowCount === 0) {
            $sheet->mergeCells("A{$firstData}:{$lastCol}{$firstData}");
            $sheet->setCellValue("A{$firstData}", 'No hay registros para los filtros seleccionados.');
            $sheet->getStyle("A{$firstData}")->applyFromArray([
                'font'      => ['italic' => true, 'color' => ['rgb' => self::MUTED]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getRowDimension($firstData)->setRowHeight(28);
        } else {
            // ── Cuerpo ───────────────────────────────────────────────
            $sheet->getStyle("A{$firstData}:{$lastCol}{$lastData}")->applyFromArray([
                'font'      => ['color' => ['rgb' => self::TEXT]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER]]],
            ]);

            for ($row = $firstData; $row <= $lastData; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(-1);
                if (($row - $firstData) % 2 === 1) {
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::ZEBRA);
                }
            }

            foreach ($columns as $i => $column) {
                $letter = Coordinate::stringFromColumnIndex($i + 1);
                $range  = "{$letter}{$firstData}:{$letter}{$lastData}";
                $type   = $column['type'] ?? 'text';

                if (in_array($type, ['date', 'time', 'number', 'status'], true)) {
                    $sheet->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
                if ($type === 'long') {
                    $sheet->getStyle($range)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
                }
                if ($type === 'status') {
                    $this->paintStatuses($sheet, $letter, $firstData, $lastData);
                }
                if (! empty($column['bold'])) {
                    $sheet->getStyle($range)->getFont()->setBold(true);
                }
            }

            $sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$lastData}");
            $this->writeSummary($sheet, $columns, $lastCol, $firstData, $lastData);
        }

        // ── Vista e impresión ────────────────────────────────────────
        $sheet->freezePane("A{$firstData}");
        $sheet->setSelectedCell("A{$firstData}");

        $setup = $sheet->getPageSetup();
        $setup->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $setup->setPaperSize(PageSetup::PAPERSIZE_LETTER);
        $setup->setFitToWidth(1);
        $setup->setFitToHeight(0);
        $setup->setRowsToRepeatAtTopByStartAndEnd($headerRow, $headerRow);

        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.6)->setLeft(0.4)->setRight(0.4);
        $sheet->getHeaderFooter()->setOddFooter(
            '&L&8Dasavena · Recursos Humanos · ' . $this->reportTitle() . '&R&8Página &P de &N'
        );
    }

    protected function paintStatuses(Worksheet $sheet, string $letter, int $from, int $to): void
    {
        for ($row = $from; $row <= $to; $row++) {
            $style = self::STATUS_STYLES[$sheet->getCell("{$letter}{$row}")->getValue()] ?? null;
            if (! $style) {
                continue;
            }
            $sheet->getStyle("{$letter}{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $style['font']]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $style['fill']]],
            ]);
        }
    }

    /** Fila de totales: número de registros, conteo por estado y suma de columnas numéricas. */
    protected function writeSummary(Worksheet $sheet, array $columns, string $lastCol, int $firstData, int $lastData): void
    {
        $row = $lastData + 2;

        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->setCellValue("B{$row}", '=SUBTOTAL(103,A' . $firstData . ':A' . $lastData . ') & " registros"');

        $statusIndex = null;
        foreach ($columns as $i => $column) {
            $letter = Coordinate::stringFromColumnIndex($i + 1);
            $type   = $column['type'] ?? 'text';

            if ($type === 'number' && ! empty($column['sum'])) {
                $sheet->setCellValue("{$letter}{$row}", "=SUBTOTAL(109,{$letter}{$firstData}:{$letter}{$lastData})");
                $sheet->getStyle("{$letter}{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle("{$letter}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            if ($type === 'status') {
                $statusIndex = $i;
            }
        }

        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font'    => ['bold' => true, 'color' => ['rgb' => self::BRAND]],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::BRAND_SOFT]],
            'borders' => [
                'top'    => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::BRAND]],
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER]],
            ],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);

        if ($statusIndex === null) {
            return;
        }

        // Resumen por estado
        $letter = Coordinate::stringFromColumnIndex($statusIndex + 1);
        $range  = "\${$letter}\${$firstData}:\${$letter}\${$lastData}";
        $labels = $this->rows()->pluck($statusIndex)->filter()->unique()->sort()->values();

        $summaryRow = $row + 2;
        $sheet->setCellValue("A{$summaryRow}", 'RESUMEN POR ESTADO');
        $sheet->getStyle("A{$summaryRow}")->getFont()->setBold(true)->setSize(9)->getColor()->setRGB(self::GOLD);

        foreach ($labels as $offset => $label) {
            $r = $summaryRow + 1 + $offset;
            $sheet->setCellValue("A{$r}", $label);
            $sheet->setCellValue("B{$r}", "=COUNTIF({$range},\"{$label}\")");
            $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $style = self::STATUS_STYLES[$label] ?? null;
            if ($style) {
                $sheet->getStyle("A{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $style['font']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $style['fill']]],
                ]);
            }
            $sheet->getStyle("A{$r}:B{$r}")->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::BORDER);
        }
    }

    // ── Subtítulos con los filtros aplicados ─────────────────────────

    public static function filtersSubtitle(?string $search, $startDate = null, $endDate = null): string
    {
        $parts = [];

        if ($startDate && $endDate) {
            $parts[] = 'Periodo: ' . Carbon::parse($startDate)->format('d/m/Y') . ' al ' . Carbon::parse($endDate)->format('d/m/Y');
        } elseif ($startDate) {
            $parts[] = 'Desde el ' . Carbon::parse($startDate)->format('d/m/Y');
        } elseif ($endDate) {
            $parts[] = 'Hasta el ' . Carbon::parse($endDate)->format('d/m/Y');
        }

        if ($search) {
            $parts[] = "Empleado: \"{$search}\"";
        }

        return $parts ? implode('  ·  ', $parts) : 'Todos los registros';
    }

    public static function weekSubtitle($start, $end): string
    {
        $from = Carbon::parse($start)->locale('es');
        $to   = Carbon::parse($end)->locale('es');

        return sprintf(
            'Semana nominal: %s %s al %s %s',
            $from->translatedFormat('l'),
            $from->format('d/m/Y'),
            $to->translatedFormat('l'),
            $to->format('d/m/Y')
        );
    }

    // ── Utilidades para las columnas ─────────────────────────────────

    protected function toExcelDate($value)
    {
        if (! $value) {
            return null;
        }

        return ExcelDate::PHPToExcel(Carbon::parse($value)->startOfDay());
    }

    protected function toExcelTime($value)
    {
        if (! $value) {
            return null;
        }

        $time = Carbon::parse($value);

        return ($time->hour * 3600 + $time->minute * 60 + $time->second) / 86400;
    }

    protected function statusLabel($value): ?string
    {
        return match (strtolower((string) $value)) {
            'aprobado', 'aprobada' => 'Aprobado',
            'rechazado', 'rechazada' => 'Rechazado',
            'pendiente' => 'Pendiente',
            '' => null,
            default => ucfirst((string) $value),
        };
    }

    protected static function folio(string $prefix, $id): string
    {
        return $prefix . '-' . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }

    protected static function inclusiveDays($from, $to): ?int
    {
        if (! $from || ! $to) {
            return null;
        }

        return (int) abs(Carbon::parse($from)->startOfDay()->diffInDays(Carbon::parse($to)->startOfDay())) + 1;
    }
}
