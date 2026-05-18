<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\TurniketEvent;
use App\Services\TurniketService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Pulls access-control events from BOTH turniket devices:
 *   - port 8002 → check-in  (direction = 'in')
 *   - port 8003 → check-out (direction = 'out')
 *
 * Steps performed each run:
 *   1. Fetch events from each device for the requested window.
 *   2. Persist raw events into `turniket_events` (deduped by port + serial_no).
 *   3. Aggregate per-employee per-day:
 *        first_in  = earliest "in"  event of the day
 *        last_out  = latest   "out" event of the day
 *      and upsert into `attendances`.
 *
 * Schedule: every 5 minutes (see App\Console\Kernel).
 *
 * Usage:
 *   php artisan turniket:sync                      # sync today, both ports
 *   php artisan turniket:sync --date=2026-05-14
 *   php artisan turniket:sync --from=2026-05-01 --to=2026-05-14
 *   php artisan turniket:sync --direction=in       # only the 8003 device
 *   php artisan turniket:sync --direction=out      # only the 8002 device
 *   php artisan turniket:sync --skip-attendance    # store events only, don't touch attendances
 */
class SyncTurniket extends Command
{
    /** @var string */
    protected $signature = 'turniket:sync
                            {--date=        : Single day to sync (YYYY-MM-DD). Defaults to today.}
                            {--from=        : Range start (YYYY-MM-DD). Used together with --to.}
                            {--to=          : Range end (YYYY-MM-DD). Used together with --from.}
                            {--direction=   : Limit to one direction: in | out. Default: both.}
                            {--skip-attendance : Persist raw events only; do not update attendances.}';

    /** @var string */
    protected $description = 'Sync turniket access events (both 8003/IN and 8002/OUT) into turniket_events and attendances';

    /** @var TurniketService */
    protected $turniket;

    public function __construct(TurniketService $turniket)
    {
        parent::__construct();
        $this->turniket = $turniket;
    }

    public function handle(): int
    {
        [$start, $end] = $this->resolveWindow();
        $directions = $this->resolveDirections();

        $this->info(sprintf(
            '[turniket:sync] window %s → %s | directions: %s',
            $start, $end, implode(',', $directions)
        ));

        $totalFetched = 0;
        $totalSaved   = 0;

        foreach ($directions as $direction) {
            try {
                $device = $this->turniket->device($direction);
            } catch (Throwable $e) {
                $this->error($e->getMessage());
                continue;
            }

            $port = (string) $device['port'];

            try {
                $events = $this->turniket->fetchEventsBetween($port, $start, $end);
            } catch (Throwable $e) {
                $this->error("Turniket API error (port {$port}/{$direction}): " . $e->getMessage());
                Log::error('[turniket:sync] API failure', [
                    'port'      => $port,
                    'direction' => $direction,
                    'error'     => $e->getMessage(),
                ]);
                continue;
            }

            $totalFetched += count($events);
            $saved         = $this->persistEvents($events, $port, $direction);
            $totalSaved   += $saved;

            $this->info(sprintf(
                '[turniket:sync] port=%s direction=%s fetched=%d saved=%d',
                $port, $direction, count($events), $saved
            ));
        }

        if (!$this->option('skip-attendance')) {
            [$created, $updated] = $this->rebuildAttendances($start, $end);
            $this->info("[turniket:sync] attendances: created={$created} updated={$updated}");
        }

        Log::info('[turniket:sync] success', [
            'fetched' => $totalFetched,
            'saved'   => $totalSaved,
        ]);

        return self::SUCCESS;
    }

    /**
     * Persist raw events into turniket_events. Dedupe on (port, serial_no).
     *
     * @return int Number of newly inserted rows.
     */
    protected function persistEvents(array $events, string $port, string $direction): int
    {
        if (empty($events)) {
            return 0;
        }

        $rows = [];
        $now  = now();

        foreach ($events as $event) {
            $serial = $event['serialNo'] ?? null;
            $time   = $event['time'] ?? null;
            if ($serial === null || $time === null) {
                continue;
            }

            // Skip device door-state events that have no person identity.
            $verifyMode = $event['currentVerifyMode'] ?? null;
            if ($verifyMode === 'invalid') {
                continue;
            }

            try {
                $carbon = Carbon::parse($time);
            } catch (Throwable $e) {
                continue;
            }

            $externalId = $event['employeeNoString']
                ?? $event['employeeNoStr']
                ?? (isset($event['employeeNo']) ? (string) $event['employeeNo'] : null);

            $rows[] = [
                'port'           => $port,
                'direction'      => $direction,
                'external_id'    => $externalId,
                'name'           => $event['name'] ?? null,
                'user_type'      => $event['userType'] ?? null,
                'serial_no'      => (int) $serial,
                'event_time'     => $carbon->toDateTimeString(),
                'event_date'     => $carbon->toDateString(),
                'event_clock'    => $carbon->format('H:i:s'),
                'major'          => $event['major'] ?? null,
                'minor'          => $event['minor'] ?? null,
                'door_no'        => $event['doorNo'] ?? null,
                'card_reader_no' => $event['cardReaderNo'] ?? null,
                'card_type'      => $event['cardType'] ?? null,
                'verify_mode'    => $event['currentVerifyMode'] ?? null,
                'mask'           => $event['mask'] ?? null,
                'picture_url'    => $event['pictureURL'] ?? null,
                'raw'            => json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        if (empty($rows)) {
            return 0;
        }

        // Determine which (port, serial_no) pairs already exist so we can return an accurate inserted count.
        $serials   = array_column($rows, 'serial_no');
        $existing  = TurniketEvent::where('port', $port)
            ->whereIn('serial_no', $serials)
            ->pluck('serial_no')
            ->all();
        $existing  = array_flip(array_map('intval', $existing));

        $newRows = array_values(array_filter($rows, function ($r) use ($existing) {
            return !isset($existing[(int) $r['serial_no']]);
        }));

        if (empty($newRows)) {
            return 0;
        }

        // Chunk to keep packet size reasonable.
        foreach (array_chunk($newRows, 200) as $chunk) {
            DB::table('turniket_events')->insertOrIgnore($chunk);
        }

        return count($newRows);
    }

    /**
     * Rebuild attendances rows for every (external_id, date) touched in the window
     * by reading directly from the turniket_events table.
     *
     * @return array{0:int,1:int} [created, updated]
     */
    protected function rebuildAttendances(Carbon $start, Carbon $end): array
    {
        $now       = Carbon::now();
        $isToday   = $start->isToday();

        // If syncing today and it's before 18:00, do NOT write last_out —
        // employees may exit briefly during the day; we only finalize last_out
        // after working hours.
        $canSetLastOut = !$isToday || $now->gte(Carbon::createFromTime(18, 0, 0));

        // Aggregate from the table — single source of truth.
        $rows = TurniketEvent::query()
            ->select('external_id', 'event_date', 'direction',
                DB::raw('MIN(event_clock) as min_clock'),
                DB::raw('MAX(event_clock) as max_clock'),
                DB::raw('MAX(name) as any_name'),
                DB::raw('MAX(user_type) as any_user_type')
            )
            ->whereNotNull('external_id')
            ->whereBetween('event_time', [$start, $end])
            ->groupBy('external_id', 'event_date', 'direction')
            ->get();

        // Pivot into [external_id][date] => ['in_min', 'out_max', 'name', 'department']
        $pivot = [];
        foreach ($rows as $r) {
            $key                 = $r->external_id;
            $date                = (string) $r->event_date;
            $pivot[$key][$date]  = $pivot[$key][$date] ?? [];

            if ($r->direction === TurniketEvent::DIRECTION_IN) {
                $pivot[$key][$date]['first_in'] = $r->min_clock;
            } elseif ($r->direction === TurniketEvent::DIRECTION_OUT) {
                $pivot[$key][$date]['last_out'] = $r->max_clock;
            }

            if (!empty($r->any_name) && empty($pivot[$key][$date]['name'])) {
                $pivot[$key][$date]['name'] = $r->any_name;
            }
            if (!empty($r->any_user_type) && empty($pivot[$key][$date]['department'])) {
                $pivot[$key][$date]['department'] = $r->any_user_type;
            }
        }

        $created = 0;
        $updated = 0;

        foreach ($pivot as $externalId => $byDate) {
            foreach ($byDate as $date => $info) {
                $attendance = Attendance::firstOrNew([
                    'external_id' => $externalId,
                    'date'        => $date,
                ]);

                $isNew = !$attendance->exists;

                $attendance->name       = $info['name'] ?? $attendance->name ?? $externalId;
                $attendance->department = $info['department'] ?? $attendance->department;

                if (!empty($info['first_in'])) {
                    if (empty($attendance->first_in) || $info['first_in'] < $attendance->first_in) {
                        $attendance->first_in = $info['first_in'];
                    }
                }

                // Only update last_out if:
                //  - it's a past date (always allowed), OR
                //  - it's today AND current time is >= 18:00
                if (!empty($info['last_out'])) {
                    $dateIsPast = Carbon::parse($date)->lt(Carbon::today());
                    if ($dateIsPast || $canSetLastOut) {
                        if (empty($attendance->last_out) || $info['last_out'] > $attendance->last_out) {
                            $attendance->last_out = $info['last_out'];
                        }
                    }
                }

                if ($isNew || $attendance->isDirty()) {
                    $attendance->save();
                    $isNew ? $created++ : $updated++;
                }
            }
        }

        return [$created, $updated];
    }

    /**
     * Resolve the list of directions to sync.
     *
     * @return array<int, string>
     */
    protected function resolveDirections(): array
    {
        $opt = $this->option('direction');
        if ($opt) {
            $opt = strtolower($opt);
            if (!in_array($opt, ['in', 'out'], true)) {
                $this->warn("Unknown --direction='{$opt}', falling back to both.");
                return array_keys($this->turniket->devices());
            }
            return [$opt];
        }

        return array_keys($this->turniket->devices());
    }

    /**
     * Resolve start/end Carbon range from CLI options.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolveWindow(): array
    {
        $from = $this->option('from');
        $to   = $this->option('to');

        if ($from && $to) {
            return [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ];
        }

        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::today();

        return [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
    }
}
