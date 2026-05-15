<?php

namespace App\Http\Controllers;

use App\Models\TurniketEvent;
use App\Services\TurniketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
