<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\TurniketEvent;
use App\Services\TurniketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Throwable;

class TurniketController extends Controller
{
    /** @var TurniketService */
    protected $turniket;

    public function __construct(TurniketService $turniket)
    {
        $this->turniket = $turniket;
    }

    /**
     * Show today's raw turniket logs from the device(s).
     *
     * Query params:
     *   ?date=YYYY-MM-DD            Single day window
     *   ?from=YYYY-MM-DD&to=...     Range window
     *   ?direction=in|out           Limit to one device (default: both)
     */
    public function getLogs(Request $request)
    {
        try {
            [$start, $end] = $this->resolveWindow($request);

            $direction = $request->query('direction');

            if ($direction === 'in' || $direction === 'out') {
                $device = $this->turniket->device($direction);
                $events = $this->turniket->fetchEventsBetween((string) $device['port'], $start, $end);
                foreach ($events as &$ev) {
                    $ev['_direction'] = $direction;
                    $ev['_port']      = $device['port'];
                }
                unset($ev);
            } else {
                $events = $this->turniket->fetchAllDevicesBetween($start, $end);
            }

            return response()->json([
                'status' => 'success',
                'from'   => $start->toDateTimeString(),
                'to'     => $end->toDateTimeString(),
                'count'  => count($events),
                'data'   => $events,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Read events from the local turniket_events table (no device call).
     */
    public function events(Request $request)
    {
        [$start, $end] = $this->resolveWindow($request);

        $query = TurniketEvent::query()
            ->whereBetween('event_time', [$start, $end])
            ->orderBy('event_time');

        if ($d = $request->query('direction')) {
            $query->where('direction', $d);
        }
        if ($eid = $request->query('external_id')) {
            $query->where('external_id', $eid);
        }

        return response()->json([
            'status' => 'success',
            'from'   => $start->toDateTimeString(),
            'to'     => $end->toDateTimeString(),
            'count'  => $query->count(),
            'data'   => $query->limit(1000)->get(),
        ]);
    }

    /**
     * Render a human-readable report: each row is one employee-day with
     * first_in, last_out, and total time spent (hours:minutes).
     *
     * Query params:
     *   ?date=YYYY-MM-DD      single day (default today)
     *   ?from=&to=            date range
     *   ?external_id=...      filter one employee
     */
    public function report(Request $request)
    {
        [$start, $end] = $this->resolveWindow($request);

        // Aggregate per employee per day (MIN/MAX naturally ignore duplicates,
        // but we still filter by distinct serial_no to be safe).
        $query = TurniketEvent::query()
            ->select(
                'external_id',
                'event_date',
                DB::raw('MIN(CASE WHEN direction = "in"  THEN event_time END) as first_in'),
                DB::raw('MAX(CASE WHEN direction = "out" THEN event_time END) as last_out'),
                DB::raw('MAX(name) as any_name'),
                DB::raw('MAX(user_type) as any_user_type')
            )
            ->whereNotNull('external_id')
            ->whereBetween('event_time', [$start, $end])
            ->groupBy('external_id', 'event_date');

        if ($eid = $request->query('external_id')) {
            $query->where('external_id', $eid);
        }

        $rows = $query->orderBy('event_date', 'desc')
            ->orderBy('first_in', 'asc')
            ->paginate(50)
            ->appends($request->query());

        // Decorate with total duration and late/early flags
        foreach ($rows as $r) {
            $r->_first_in_clock = $r->first_in ? Carbon::parse($r->first_in)->format('H:i') : null;
            $r->_last_out_clock  = $r->last_out ? Carbon::parse($r->last_out)->format('H:i') : null;

            if ($r->first_in && $r->last_out) {
                $diff  = Carbon::parse($r->first_in)->diff(Carbon::parse($r->last_out));
                $r->_spent = sprintf('%02d:%02d', $diff->h + ($diff->d * 24), $diff->i);
            } else {
                $r->_spent = '-';
            }

            $r->_is_late  = $r->_first_in_clock && $r->_first_in_clock > '09:00';
            $r->_is_early = $r->_last_out_clock && $r->_last_out_clock < '18:00';
        }

        return view('turniket_events.index', [
            'rows'   => $rows,
            'start'  => $start->toDateString(),
            'end'    => $end->toDateString(),
            'day'    => $request->query('date'),
            'eid'    => $request->query('external_id'),
        ]);
    }

    /**
     * Detailed timeline: every entry/exit pair per employee per day.
     *
     * Shows chronologically:
     *   Visit 1: 06:33 IN → 07:45 OUT = 1:12
     *   Visit 2: 08:50 IN → 09:04 OUT = 0:14
     *   Total: 1:26
     */
    public function timeline(Request $request)
    {
        [$start, $end] = $this->resolveWindow($request);
        $now = Carbon::now();

        // Work zone filters
        $workZoneId      = $request->input('work_zone_id');
        $childWorkZoneId = $request->input('child_work_zone_id');

        // Build user filter query to get ch_ids by work zone
        $userQuery = \App\Models\User::query()
            ->whereNotNull('ch_id');

        if ($childWorkZoneId) {
            $userQuery->where('work_zone_id', $childWorkZoneId);
        } elseif ($workZoneId) {
            $userQuery->where(function ($q) use ($workZoneId) {
                $q->where('work_zone_id', $workZoneId)
                  ->orWhereIn('work_zone_id', function ($sub) use ($workZoneId) {
                      $sub->select('id')
                          ->from('work_zones')
                          ->where('parent_id', $workZoneId);
                  });
            });
        }

        // Get paginated external_ids (ch_ids) so we only show 50 employees per page
        $externalIdsPaginated = $userQuery->clone()
            ->select('ch_id')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(20)
            ->appends($request->query());

        $allowedExternalIds = $externalIdsPaginated->pluck('ch_id')->all();

        if (empty($allowedExternalIds)) {
            return view('turniket_events.timeline', [
                'employees' => collect(),
                'day'       => $request->query('date'),
                'dateLabel' => $start->format('d.m.Y'),
                'pager'     => $externalIdsPaginated,
            ]);
        }

        $events = TurniketEvent::query()
            ->whereIn('external_id', $allowedExternalIds)
            ->whereBetween('event_time', [$start, $end])
            ->orderBy('event_time')
            ->get();

        // Pre-load user names by ch_id for better display
        $userNames = $userQuery->clone()
            ->get(['ch_id', 'first_name', 'last_name', 'work_zone_id'])
            ->mapWithKeys(function ($u) {
                $name = trim(((string) $u->first_name) . ' ' . ((string) $u->last_name));
                return [$u->ch_id => $name !== '' ? $name : null];
            })
            ->toArray();

        // Group by person
        $grouped = [];
        foreach ($events as $ev) {
            $key = $ev->external_id;
            $grouped[$key]['events'][] = $ev;
        }

        // Build visits (in → out pairs) for each person
        $employees = [];
        foreach ($allowedExternalIds as $externalId) {
            $data = $grouped[$externalId] ?? ['events' => []];
            $deduped = $this->deduplicateEvents($data['events']);

            $visits       = [];
            $currentIn    = null;
            $totalSeconds = 0;

            foreach ($deduped as $ev) {
                if ($ev->direction === TurniketEvent::DIRECTION_IN) {
                    $currentIn = $ev;
                } elseif ($ev->direction === TurniketEvent::DIRECTION_OUT) {
                    if ($currentIn !== null) {
                        $seconds = Carbon::parse($currentIn->event_time)
                            ->diffInSeconds(Carbon::parse($ev->event_time));
                        $visits[] = [
                            'in'      => $currentIn,
                            'out'     => $ev,
                            'seconds' => $seconds,
                            'status'  => 'closed',
                        ];
                        $totalSeconds += $seconds;
                        $currentIn = null;
                    } else {
                        $visits[] = [
                            'in'      => null,
                            'out'     => $ev,
                            'seconds' => null,
                            'status'  => 'unmatched_exit',
                        ];
                    }
                }
            }

            // Final open visit — still inside
            if ($currentIn !== null) {
                $running = Carbon::parse($currentIn->event_time)->diffInSeconds($now);
                $visits[] = [
                    'in'      => $currentIn,
                    'out'     => null,
                    'seconds' => $running,
                    'status'  => 'open',
                ];
                $totalSeconds += $running;
            }

            $employees[] = [
                'external_id'   => $externalId,
                'name'          => $userNames[$externalId] ?? $externalId,
                'visits'        => $visits,
                'total_seconds' => $totalSeconds,
                'total_time'    => $this->formatDuration($totalSeconds),
            ];
        }

        usort($employees, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return view('turniket_events.timeline', [
            'employees' => $employees,
            'day'       => $request->query('date'),
            'dateLabel' => $start->format('d.m.Y'),
            'pager'     => $externalIdsPaginated,
        ]);
    }

    /**
     * Remove duplicate events: same direction within N seconds.
     * Keeps the first event in each cluster.
     */
    protected function deduplicateEvents(array $events, int $thresholdSeconds = 60): array
    {
        $out = [];
        $last = null;

        foreach ($events as $ev) {
            if ($last !== null
                && $last->direction === $ev->direction
                && Carbon::parse($last->event_time)->diffInSeconds(Carbon::parse($ev->event_time)) < $thresholdSeconds
            ) {
                continue; // skip duplicate
            }
            $out[] = $ev;
            $last = $ev;
        }

        return $out;
    }

    /**
     * Format seconds into H:MM or HH:MM.
     */
    protected function formatDuration(int $seconds): string
    {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        return sprintf('%d:%02d', $h, $m);
    }

    /**
     * Trigger a manual sync (handy for ops / debugging from the browser).
     */
    public function sync(Request $request)
    {
        $exitCode = Artisan::call('turniket:sync', array_filter([
            '--date'      => $request->query('date'),
            '--from'      => $request->query('from'),
            '--to'        => $request->query('to'),
            '--direction' => $request->query('direction'),
        ]));

        return response()->json([
            'status'    => $exitCode === 0 ? 'success' : 'error',
            'exit_code' => $exitCode,
            'output'    => Artisan::output(),
        ]);
    }

    /**
     * Resolve the date window from the request.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolveWindow(Request $request): array
    {
        if ($request->filled('from') && $request->filled('to')) {
            return [
                Carbon::parse($request->query('from'))->startOfDay(),
                Carbon::parse($request->query('to'))->endOfDay(),
            ];
        }

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))
            : Carbon::today();

        return [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
    }
}
