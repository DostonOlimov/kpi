<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class AttendanceImport2 implements ToModel, WithStartRow
{
    public function model(array $row)
    {

        if (!isset($row[1]) || empty($row[1])) {
            return null;
        }

        $externalId = trim($row[1]);
        $name       = trim($row[2]);
        $department = trim($row[3]);
        $date       = trim($row[4]);
        $lastOut    = trim($row[7]);

        // Find existing record for this user and date, or create a new one
        $attendance = Attendance::firstOrNew([
            'external_id' => $externalId,
            'date'        => $date,
        ]);

        $attendance->fill([
            'name'       => $name,
            'department' => $department,
            'last_out'   => ($lastOut !== '-' && !empty($lastOut)) ? $lastOut : $attendance->last_out,
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
