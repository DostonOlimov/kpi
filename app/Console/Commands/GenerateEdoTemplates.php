<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateEdoTemplates extends Command
{
    protected $signature = 'edo:generate-templates';

    protected $description = 'Generate example Excel template files for EDO document import';

    public function handle()
    {
        $this->generateTemplate1();
        $this->generateTemplate2();

        $this->info('Templates generated successfully!');
        return 0;
    }

    private function generateTemplate1(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers matching the actual EDO document template
        $headers = [
            'kirish_raqami',
            'kiruvchi_sana',
            'shoshilning',
            'xdfu_dsp',
            'bajarish_muddati',
            'otib_ketgan_kunlar',
            'topshiriq_holati',
            'asosiy_ijrochi',
            'javob_berilgan_vaqt',
        ];
        foreach ($headers as $col => $header) {
            $sheet->setCellValue([$col + 1, 1], $header);
        }

        // Example row
        $sheet->fromArray(
            ['XDFU/102', '31.03.2026', '\u0419\u04e3\u049b', '\u0425\u0430', '17.04.2026', 15, '\u0411\u0430\u0436\u0430\u0440\u0438\u043b\u043c\u0430\u0433\u0430\u043d', 'D.R.FARMONOV', '17.04.2026 16:02'],
            null,
            'A2'
        );

        // Auto-size columns
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // Style header row
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $writer->save(public_path('templates/edo_shablon_1.xlsx'));
        $this->info('Generated: public/templates/edo_shablon_1.xlsx');
    }

    private function generateTemplate2(): void
    {
        // No longer needed — single template only
        $this->info('Skipped: Template 2 (not used)');
    }
}
