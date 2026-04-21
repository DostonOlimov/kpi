<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Services\HtmlXlsParser;

class AttendanceImport2
{
    public function import(string $filePath): array
    {
        $rows   = HtmlXlsParser::parse($filePath, startRow: 6);
        $errors = [];
        $count  = 0;

        foreach ($rows as $index => $row) {
            try {
                if (!isset($row[1]) || !is_numeric(trim($row[1]))) continue;
                if (!isset($row[0]) || !is_numeric(trim($row[0]))) continue;

                $externalId = trim($row[1]);
                $date       = $this->parseDate($row[6] ?? '');
                $lastOut    = $this->parseTime($row[10] ?? '');

                if (empty($date)) continue;

                $attendance = Attendance::firstOrNew([
                    'external_id' => $externalId,
                    'date'        => $date,
                ]);

                $attendance->fill([
                    'name'       => trim($row[2] ?? ''),
                    'department' => trim($row[3] ?? ''),
                    'last_out'   => $lastOut ?: $attendance->last_out,
                    'created_by' => auth()->user()->username ?? 'system',
                ]);

                $attendance->save();
                $count++;

            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 6) . ": " . $e->getMessage();
            }
        }

        return ['imported' => $count, 'errors' => $errors];
    }

    private function parseDate($value): string
    {
        $clean = trim((string) $value);
        if (empty($clean) || $clean === '-') return '';
        return substr($clean, 0, 10);
    }

    private function parseTime($value): ?string
    {
        $clean = trim((string) $value);
        if (empty($clean) || $clean === '-') return null;
        return preg_match('/^\d{2}:\d{2}:\d{2}$/', $clean) ? $clean : null;
    }
}