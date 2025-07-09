<?php

namespace App\Services;

use App\Models\Task;
use PhpOffice\PhpWord\IOFactory as WordIO;
use PhpOffice\PhpSpreadsheet\IOFactory as ExcelIO;
use Smalot\PdfParser\Parser as PdfParser;
use Intervention\Image\Facades\Image;
use OpenAI;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class EmployeeTaskScoringService
{
    public function extractText($path): string
    {

        if (is_null($path)) {
            return '';
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'docx' => $this->extractFromDocx($path),
            'pdf'  => $this->extractFromPdf($path),
            'xlsx' => $this->extractFromXlsx($path),
            default => throw new \Exception("Unsupported file format"),
        };
    }

    protected function extractFromDocx($path): string
    {
        $phpWord = WordIO::load($path, 'Word2007');
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . " ";
                }
            }
        }
        return trim($text);
    }

    protected function extractFromPdf($path): string
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($path);
        return $pdf->getText();
    }

    protected function extractFromXlsx($path): string
    {
        $spreadsheet = ExcelIO::load($path);
        $text = '';
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            foreach ($sheet->toArray() as $row) {
                $text .= implode(" ", $row) . " ";
            }
        }
        return trim($text);
    }

    protected function extractFromImage($path): string
    {
        // Tesseract OCR must be installed on the server
        $imagePath = escapeshellarg($path);
        $output = shell_exec("tesseract $imagePath stdout -l uzb+rus");
        return trim($output ?? '');
    }

    public function scoreText(string $text): array
    {
        $textLength = Str::length($text);

        $score = 0;
        $feedback = [];

        // Quantity
        if ($textLength > 1000) {
            $score += 3;
            $feedback[] = "Excellent coverage of tasks.";
        } elseif ($textLength > 500) {
            $score += 2;
            $feedback[] = "Moderate amount of content.";
        } else {
            $score += 1;
            $feedback[] = "Too short; please add more detail.";
        }

        // Quality: presence of keywords (e.g., task results, goals, outcome)
        $qualityKeywords = ['лойиҳа', 'натижа', 'кўриб чиқилди', 'таклиф', 'жиҳатлари'];
        $matched = collect($qualityKeywords)->filter(fn($k) => Str::contains($text, $k))->count();

        if ($matched >= 3) {
            $score += 3.5;
            $feedback[] = "Well-structured and relevant.";
        } elseif ($matched == 2) {
            $score += 2;
            $feedback[] = "Some relevant information.";
        } else {
            $score += 1;
            $feedback[] = "Needs better structure and relevance.";
        }

        // Timeliness (assume you check date elsewhere)
        $score += 3; // Placeholder
        $feedback[] = "Timely submission (assumed).";

        return [
            'score' => min(10, round($score, 1)),
            'feedback' => implode(" ", $feedback),
        ];
    }

    public function scoreWithGemini(string $text, $kpi, $task): array
    {
        // Requires Google Gemini API client installed and configured
        $apiKey = config('services.gemini.api_key'); // define in .env
       $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

        $prompt = <<<EOT
You are a performance evaluator.

Evaluate the following employee report text for the KPI "{$kpi->name}" (maximum score: {$kpi->max_score}).
The related task is "{$task->name}": {$task->description}
The employee's attached file's text is: {$text}

Rate the report on a scale of 1 to {$kpi->max_score} considering:
1. Quantity of tasks completed
2. Quality of the report
3. how relevant it is to the task

and give feedback in uzbek language.

Your response must follow this format:
Score: <score out of {$kpi->max_score}>
Feedback: <short explanation>
EOT;

        $response = Http::post($url, [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ]);

        $result = $response->json();
        $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? 'Score: 5\nFeedback: No reply.';

        preg_match('/Score:\s*(\d+)/i', $content, $scoreMatch);
        preg_match('/Feedback:\s*(.+)/is', $content, $feedbackMatch);

        return [
            'score' => isset($scoreMatch[1]) ? (int)$scoreMatch[1] : null,
            'feedback' => $feedbackMatch[1] ?? 'No feedback generated.',
        ];
    }
}
