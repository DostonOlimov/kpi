<?php

namespace App\Services;

use Carbon\Carbon;
use RuntimeException;

/**
 * Service for communicating with the Hikvision turniket (access control) devices.
 *
 * Two physical devices are configured in config/services.php:
 *   - direction "in"  (port 8003) → check-in events
 *   - direction "out" (port 8002) → check-out events
 *
 * The same credentials and timezone are shared by both devices.
 */
class TurniketService
{
    /** @var array */
    protected $config;

    public function __construct(array $config = null)
    {
        $this->config = $config ?: (array) config('services.turniket');
    }

    /**
     * Get the list of configured devices.
     *
     * @return array<string, array{port:string, direction:string}>
     */
    public function devices(): array
    {
        return $this->config['devices'] ?? [];
    }

    /**
     * Resolve a single device descriptor by direction.
     */
    public function device(string $direction): array
    {
        $devices = $this->devices();
        if (!isset($devices[$direction])) {
            throw new RuntimeException("Unknown turniket direction: {$direction}");
        }
        return $devices[$direction];
    }

    /**
     * Fetch a single page of access-control events from a specific port.
     */
    public function fetchEventsPage(string $port, string $startTime, string $endTime, int $position = 0, ?int $maxResults = null): array
    {
        $url = sprintf(
            'http://%s:%s/ISAPI/AccessControl/AcsEvent?format=json',
            $this->config['ip'],
            $port
        );

        $payload = json_encode([
            'AcsEventCond' => [
                'searchID'             => uniqid('', true),
                'searchResultPosition' => $position,
                'maxResults'           => $maxResults ?: (int) ($this->config['page_size'] ?? 100),
                'major'                => 0,
                'minor'                => 0,
                'startTime'            => $startTime,
                'endTime'              => $endTime,
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($payload),
        ]);
        curl_setopt($ch, CURLOPT_HTTPAUTH,       CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD,        $this->config['user'] . ':' . $this->config['pass']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int) ($this->config['connect_timeout'] ?? 5));
        curl_setopt($ch, CURLOPT_TIMEOUT,        (int) ($this->config['timeout'] ?? 15));

        $raw       = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new RuntimeException(
                sprintf('Turniket API HTTP %d on port %s: %s | %s', $httpCode, $port, $curlError ?: 'no curl error', (string) $raw)
            );
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Turniket API returned invalid JSON: ' . substr((string) $raw, 0, 500));
        }

        return $decoded['AcsEvent'] ?? [];
    }

    /**
     * Fetch ALL events from a single port between two timestamps, with pagination.
     *
     * @return array<int, array>
     */
    public function fetchEventsBetween(string $port, Carbon $start, Carbon $end): array
    {
        $tz       = $this->config['timezone'] ?? '+05:00';
        $startStr = $start->format('Y-m-d\TH:i:s') . $tz;
        $endStr   = $end->format('Y-m-d\TH:i:s') . $tz;
        $pageSize = (int) ($this->config['page_size'] ?? 100);

        $all       = [];
        $position  = 0;
        $safetyCap = 200;

        do {
            $page = $this->fetchEventsPage($port, $startStr, $endStr, $position, $pageSize);

            $events = $page['InfoList'] ?? [];
            if (!empty($events)) {
                $all = array_merge($all, $events);
            }

            $numOfMatches = (int) ($page['numOfMatches'] ?? count($events));
            $totalMatches = (int) ($page['totalMatches'] ?? 0);
            $responseStat = $page['responseStatusStrg'] ?? null;

            $position += $numOfMatches;

            $hasMore = $numOfMatches > 0
                && $position < $totalMatches
                && $responseStat !== 'NO MATCH';

            $safetyCap--;
        } while ($hasMore && $safetyCap > 0);

        return $all;
    }

    /**
     * Fetch events from every configured device, tagging each with its direction & port.
     *
     * @return array<int, array> Each event is the original payload with two extra keys:
     *                           '_direction' ('in'|'out') and '_port' (string).
     */
    public function fetchAllDevicesBetween(Carbon $start, Carbon $end): array
    {
        $merged = [];

        foreach ($this->devices() as $key => $device) {
            $port      = (string) $device['port'];
            $direction = $device['direction'];

            $events = $this->fetchEventsBetween($port, $start, $end);
            foreach ($events as $event) {
                $event['_direction'] = $direction;
                $event['_port']      = $port;
                $merged[] = $event;
            }
        }

        return $merged;
    }
}
