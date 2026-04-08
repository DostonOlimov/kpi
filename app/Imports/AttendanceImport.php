<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class AttendanceImport implements ToModel, WithStartRow
{
    public function model(array $row)
    {
        // Based on your image:
        // Index 1 (B): External ID
        // Index 2 (C): Name
        // Index 3 (D): Department
        // Index 4 (E): Date (YYYY-MM-DD)
        // Index 7 (H): First In (HH:MM:SS)
        // Index 8 (I): Last Out / Comments

        if (!isset($row[1]) || empty($row[1])) {
            return null;
        }

        $externalId = trim($row[1]);
        $name       = trim($row[2]);
        $department = trim($row[3]);
        $date       = trim($row[4]);
        $firstIn    = trim($row[7]);
        $lastOutRaw = trim($row[8]);

        // Logic to distinguish between a Time and a Comment in the Last Out column
        $lastOut = null;
        $comment = null;

        if (!empty($lastOutRaw)) {
            // Check if string contains a time format (e.g., 18:05:20)
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $lastOutRaw)) {
                $lastOut = $lastOutRaw;
            } else {
                // It's a text comment (e.g., "меҳнат таътилида")
                $comment = $lastOutRaw;
            }
        }

        // Find existing record for this user and date, or create a new one
        $attendance = Attendance::firstOrNew([
            'external_id' => $externalId,
            'date'        => $date,
        ]);

        $attendance->fill([
            'name'       => $name,
            'department' => $department,
            'first_in'   => ($firstIn !== '-' && !empty($firstIn)) ? $firstIn : $attendance->first_in,
            'last_out'   => $lastOut ?: $attendance->last_out,
            'comment'    => $comment ?: $attendance->comment,
            'created_by' => auth()->user()->username ?? 'system',
        ]);

        return $attendance;
    }

    public function startRow(): int
    {
        // Based on the image, data starts on row 4 
        // (Row 1: Title, Row 2: Date Range, Row 3: Headers)
        return 4; 
    }
}
