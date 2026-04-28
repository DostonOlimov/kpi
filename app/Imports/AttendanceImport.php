<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Services\HtmlXlsParser;

class AttendanceImport
{
    public function import(string $filePath): array
    {
        $rows   = HtmlXlsParser::parse($filePath);
        $errors = [];
        $count  = 0;

        foreach ($rows as $index => $row) {
            try {
                if (count($row) < 10) continue;

                $externalId = trim($row[1]); // Идентификатор человека
                $name       = trim($row[2]); // Имя
                $department = trim($row[3]); // Департамент
                $position   = trim($row[4]); // Должность
                $date       = $this->parseDate($row[6]); // Дата
                // [5] = Пол, [7] = День недели, [8] = Расписание — skipped

                // Column 9 = "Записи": all timestamps space-separated
                [$firstIn, $lastOut] = $this->parseTimestamps($row[9]);

                // Skip header rows or rows without a numeric external_id
                if (empty($externalId) || !is_numeric($externalId)) continue;
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

    /**
     * Parse a cell with multiple space/newline-separated HH:MM:SS timestamps.
     * Returns [first_in, last_out].
     */
    private function parseTimestamps($value): array
    {
        $clean = trim((string) $value);

        if (empty($clean) || $clean === '-') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        $times = array_values(
            array_filter($parts, fn($t) => preg_match('/^\d{2}:\d{2}:\d{2}$/', $t))
        );

        if (empty($times)) {
            return [null, null];
        }

        return [
            $times[0],
            count($times) > 1 ? $times[count($times) - 1] : null,
        ];
    }

    private function parseDate($value): string
    {
        $clean = trim((string) $value);
        if (empty($clean) || $clean === '-') return '';
        return substr($clean, 0, 10); // "2026-04-28 00:00:00" → "2026-04-28"
    }
}