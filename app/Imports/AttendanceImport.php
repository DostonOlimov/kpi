<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Services\HtmlXlsParser;

class AttendanceImport
{
    public function import(string $filePath): array
    {
        $rows   = HtmlXlsParser::parse($filePath); // no startRow needed anymore
        $errors = [];
        $count  = 0;

        foreach ($rows as $index => $row) {
            try {
                $externalId = trim($row[1]);
                $name       = trim($row[2] ?? '');
                $department = trim($row[3] ?? '');
                $position   = trim($row[4] ?? '');
                $date       = $this->parseDate($row[6] ?? '');
                $firstIn    = $this->parseTime($row[9] ?? '');
                $lastOut    = $this->parseTime($row[10] ?? '');

                if (empty($date)) continue;

                $attendance = Attendance::firstOrNew([
                    'external_id' => $externalId,
                    'date'        => $date,
                ]);

                $attendance->fill([
                    'name'       => $name,
                    'department' => $department,
                    'position'   => $position,
                    'first_in'   => $firstIn  ?: $attendance->first_in,
                    'last_out'   => $lastOut  ?: $attendance->last_out,
                    'created_by' => auth()->user()->username ?? 'system',
                ]);

                $attendance->save();
                $count++;

            } catch (\Exception $e) {
                $errors[] = "Row {$index}: " . $e->getMessage();
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