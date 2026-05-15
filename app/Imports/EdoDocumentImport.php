<?php

namespace App\Imports;

use App\Models\Edodocument;
use App\Models\EdoUserName;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class EdoDocumentImport extends DefaultValueBinder implements WithCustomValueBinder, ToCollection, WithHeadingRow, WithChunkReading
{
    private int $createdBy;
    private int $importedCount = 0;
    private array $errors = [];
    private int $globalRowIndex = 0;

    public function __construct(int $createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * Skip formatting — only bind simple types to save memory.
     */
    public function bindValue(Cell $cell, $value): bool
    {
        $cell->setValueExplicit($value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        return true;
    }

    public function collection(Collection $rows)
    {
        $batch = [];

        foreach ($rows as $row) {
            $this->globalRowIndex++;
            try {
                $data = $this->mapRow($row);

                if ($data === null) {
                    continue;
                }

                $data['created_by'] = $this->createdBy;
                $batch[] = $data;
            } catch (\Exception $e) {
                $this->errors[] = "Qator " . ($this->globalRowIndex + 1) . ": " . $e->getMessage();
            }
        }

        if (!empty($batch)) {
            try {
                DB::transaction(function () use ($batch) {
                    foreach ($batch as $data) {
                        Edodocument::updateOrCreate(
                            ['document_number' => $data['document_number']],
                            $data
                        );
                    }
                });
                $this->importedCount += count($batch);
            } catch (\Exception $e) {
                $this->errors[] = "Batch (" . count($batch) . " rows): " . $e->getMessage();
            }
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Map a single row from the EDO template.
     *
     * Columns: kirish_raqami, kiruvchi_sana, shoshilning, xdfu_dsp,
     *          bajarish_muddati, otib_ketgan_kunlar, topshiriq_holati,
     *          asosiy_ijrochi, javob_berilgan_vaqt
     */
    private function mapRow(Collection $row): ?array
    {
        $docNumber   = $this->getVal($row, ['kiris_raqami']);
        $docDate     = $this->getVal($row, ['kiruvci_sana']);
        $urgent      = $this->getVal($row, ['sosilinc']);
        $xdfu        = $this->getVal($row, ['xdfu_dsp']);
        $dueDate     = $this->getVal($row, ['bazaris_muddati']);
        $overdueDays = $this->getVal($row, ['tib_ketgan_kunlar']);
        $taskStatus  = $this->getVal($row, ['topsiriq_olati']);
        $executor    = $this->getVal($row, ['asosii_izroci']);
        $responseAt  = $this->getVal($row, ['zavob_berilgan_vaqt']);

        if (empty($docNumber) || empty($docDate) || empty($dueDate)) {
            return null;
        }

        // Resolve user_id from executor name via edo_user_names
        $userId = $this->resolveUser($executor);

        // Map task status to internal status
        $status = $this->mapStatus($taskStatus);

        // Store extra fields in JSON data column
        $data = [
            'document_number' => $docNumber,
            'document_date'   => $this->parseDate($docDate),
            'document_type'   => 'Kiruvchi hujjat',
            'sender'          => null,
            'due_date'        => $this->parseDate($dueDate),
            'task_created_at' => $this->parseDate($docDate),
            'summary'         => null,
            'status'          => $status,
            'user_id'         => $userId,
            'type'            => 'kiruvchi',
            'data'            => array_filter([
                'shoshilning'        => $urgent,
                'xdfu_dsp'           => $xdfu,
                'otib_ketgan_kunlar' => $overdueDays ? (int) $overdueDays : null,
                'topshiriq_holati'   => $taskStatus,
                'asosiy_ijrochi'     => $executor,
                'javob_berilgan_vaqt'=> $responseAt,
            ]),
        ];

        // Set completed_at if status indicates completion
        if (in_array($status, ['vaqtida_bajarilgan', 'muddati_o_tib_bajarilgan'])) {
            $data['completed_at'] = $responseAt ? $this->parseDateTime($responseAt) : Carbon::now();
        }

        return $data;
    }

    /**
     * Resolve user_id from executor name using edo_user_names table.
     */
    private function resolveUser(?string $executorName): ?int
    {
        if (empty($executorName)) {
            return null;
        }

        $edoName = EdoUserName::where('name', $executorName)->first();
        if ($edoName) {
            return $edoName->user_id;
        }

        return null;
    }

    /**
     * Map Uzbek status text to internal status.
     */
    private function mapStatus(?string $status): string
    {
        if (empty($status)) {
            return 'pending';
        }

        $map = [
            'Бажарилган'            => 'vaqtida_bajarilgan',
            'Бажарилмаган'          => 'pending',
            'Бажарилмоқда'          => 'in_progress',
            'Муддатидан кеч бажарилган' => 'muddati_o_tib_bajarilgan',
        ];

        return $map[$status] ?? 'pending';
    }

    private function getVal(Collection $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $row[$key] ?? null;
            if ($value !== null && $value !== '') {
                return (string) $value;
            }
        }
        return null;
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $date->format('Y-m-d');
            }

            $formats = ['d.m.Y', 'Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, trim($value))->format('Y-m-d');
                } catch (\Exception $e) {
                    continue;
                }
            }

            return Carbon::parse(trim($value))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseDateTime($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $dt->format('Y-m-d H:i:s');
            }

            $formats = ['d.m.Y H:i', 'Y-m-d H:i:s', 'd.m.Y H:i:s', 'Y-m-d H:i'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, trim($value))->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    continue;
                }
            }

            return Carbon::parse(trim($value))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return Carbon::now()->format('Y-m-d H:i:s');
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
