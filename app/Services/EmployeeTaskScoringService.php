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
        if (is_null($path) || empty($path)) {
            return '';
        }

        // Check if file exists
        if (!file_exists($path)) {
            throw new \Exception("File not found: {$path}");
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'docx' => $this->extractFromDocx($path),
            'pdf'  => $this->extractFromPdf($path),
            'xlsx' => $this->extractFromXlsx($path),
            'xls'  => $this->extractFromXlsx($path),
            'doc'  => $this->extractFromDocx($path),
            default => throw new \Exception("Unsupported file format: {$extension}"),
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
        
        if (!$apiKey) {
            throw new \Exception('Gemini API key not configured');
        }
        
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}";

        $maxScore = $kpi->max_score ?? 10;
        
        $prompt = <<<EOT
You are a performance evaluator.

Evaluate the following employee report for the KPI "{$kpi->name}" (maximum score: {$maxScore}).

TASK INFORMATION:
Task Name: {$task->name}
Task Description: {$task->description}

EMPLOYEE'S SUBMISSION:
{$text}

Rate the submission on a scale of 1 to {$maxScore} considering:
1. Quantity of tasks completed
2. Quality of the report
3. Relevance to the task

IMPORTANT: You must provide both a score and feedback in Uzbek language.

Your response must follow this EXACT format:
Score: <number from 1 to {$maxScore}>
Feedback: <2-3 sentences explanation in Uzbek>
EOT;

        try {
            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gemini API error: ' . $response->body());
            }

            $result = $response->json();
            $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($content)) {
                throw new \Exception('Empty response from Gemini API');
            }

            // Extract score
            preg_match('/Score:\s*(\d+(?:\.\d+)?)/i', $content, $scoreMatch);
            $score = isset($scoreMatch[1]) ? (float)$scoreMatch[1] : null;

            // Ensure score is within valid range
            if ($score !== null) {
                $score = min($maxScore, max(1, $score));
            }

            // Extract feedback
            preg_match('/Feedback:\s*(.+?)(?:\n|$)/is', $content, $feedbackMatch);
            $feedback = isset($feedbackMatch[1]) ? trim($feedbackMatch[1]) : '';

            // Provide default feedback if empty
            if (empty($feedback)) {
                $feedback = "Vazifa bajarildi. Natija qoniqarli.";
            }

            // Provide default score if AI failed to provide one
            if ($score === null) {
                $score = round($maxScore * 0.7); // Default to 70%
            }

            return [
                'score' => round($score, 1),
                'feedback' => $feedback,
            ];
        } catch (\Exception $e) {
            throw new \Exception('Gemini API call failed: ' . $e->getMessage());
        }
    }
}
