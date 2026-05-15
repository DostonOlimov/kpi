<?php

namespace App\Jobs;

use App\Imports\EdoDocumentImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ImportEdoDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $filePath;
    private int $createdBy;
    private string $cacheKey;

    public $timeout = 600;

    public function __construct(string $filePath, int $createdBy, string $cacheKey)
    {
        $this->filePath  = $filePath;
        $this->createdBy = $createdBy;
        $this->cacheKey  = $cacheKey;
    }

    public function handle(): void
    {
        ini_set('memory_limit', '2048M');
        Cache::put($this->cacheKey, ['status' => 'processing'], 3600);

        try {
            // Convert XLSX to CSV first — uses far less memory
            $csvPath = $this->convertToCsv($this->filePath);

            $import = new EdoDocumentImport($this->createdBy);
            Excel::import($import, $csvPath, \Maatwebsite\Excel\Excel::CSV);

            Cache::put($this->cacheKey, [
                'status'  => 'completed',
                'count'   => $import->getImportedCount(),
                'errors'  => $import->getErrors(),
            ], 3600);

            // Clean up CSV
            if ($csvPath !== $this->filePath && file_exists($csvPath)) {
                @unlink($csvPath);
            }
        } catch (\Exception $e) {
            Cache::put($this->cacheKey, [
                'status'  => 'failed',
                'message' => $e->getMessage(),
            ], 3600);
        }

        // Clean up temp file
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }

    /**
     * Convert XLSX to CSV using PhpSpreadsheet with ReadFilter
     * to minimize memory. Only reads first sheet.
     */
    private function convertToCsv(string $xlsxPath): string
    {
        $ext = pathinfo($xlsxPath, PATHINFO_EXTENSION);

        // If already CSV, return as-is
        if (strtolower($ext) === 'csv') {
            return $xlsxPath;
        }

        $csvPath = \sys_temp_dir() . '/' . basename($xlsxPath, '.' . $ext) . '.csv';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($xlsxPath);
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);

        // Only read first sheet
        if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\IReader) {
            $reader->setLoadSheetsOnly([0]);
        }

        $spreadsheet = $reader->load($xlsxPath);
        $sheet = $spreadsheet->getActiveSheet();

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->save($csvPath);

        // Free memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        gc_collect_cycles();

        return $csvPath;
    }
}
