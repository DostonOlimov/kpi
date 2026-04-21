<?php

namespace App\Services;

class HtmlXlsParser
{
    /**
     * Parse an HTML-disguised .xls attendance file.
     *
     * The file has broken HTML — after the first <tr>, remaining rows
     * have <td> tags floating directly inside <table> without <tr> wrappers.
     * BeautifulSoup/DomCrawler only sees 1 row because of this.
     *
     * Fix: extract ALL <td> values from the Punch_Report section using
     * regex, then group them into rows of 11 (the column count).
     */
    public static function parse(string $filePath): array
    {
        $content = file_get_contents($filePath);

        // Fix encoding if needed
        $encoding = mb_detect_encoding($content, ['UTF-8', 'Windows-1251', 'ISO-8859-1'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        // Extract only the data section (between Punch_Report comments)
        $start = strpos($content, '<!-- SECTION: Punch_Report -->');
        if ($start === false) {
            // Fallback: try to find the data table by class
            $start = strpos($content, 'class="Punch_Report"');
            if ($start === false) {
                // Last resort: use entire body
                $start = 0;
            }
        }

        // Find the next SECTION comment to know where data ends
        $end = strpos($content, '<!-- SECTION:', $start + 1);
        $section = $end !== false
            ? substr($content, $start, $end - $start)
            : substr($content, $start);

        // Extract all <td> cell values via regex (handles missing <tr> wrappers)
        preg_match_all('/<td[^>]*>(.*?)<\/td>/is', $section, $matches);
        $cells = array_map(function ($cell) {
            // Strip any inner HTML tags and decode entities
            return trim(html_entity_decode(strip_tags($cell), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }, $matches[1]);

        if (empty($cells)) {
            return [];
        }

        // Group flat cell array into rows of 11 columns
        $columnsPerRow = 11;
        $rows = array_chunk($cells, $columnsPerRow);

        // Filter out any incomplete rows or non-data rows
        return array_values(array_filter($rows, function ($row) use ($columnsPerRow) {
            return count($row) === $columnsPerRow
                && isset($row[0], $row[1])
                && is_numeric(trim($row[0]))   // col 0: row number
                && is_numeric(trim($row[1]));  // col 1: person ID
        }));
    }
}