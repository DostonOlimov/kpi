<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\Score;
use App\Models\UserKpi;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\EmployeeDays;
use App\Models\Month;
use App\Models\User;
use App\Models\WorkZone;

class EmployeeDaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function list(Request $request)
    {
        $month_id = session('month') ?? (int) date('m');
        $year = session('year') ?? (int) date('Y');

        $parentWorkZones = WorkZone::whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $selectedParentId = $request->query('work_zone_parent');
        $selectedChildId = $request->query('work_zone_id');

        $childWorkZones = collect();
        if ($selectedParentId) {
            $childWorkZones = WorkZone::where('parent_id', $selectedParentId)
                ->orderBy('name')
                ->get();
        }

        $query = User::with(['working_days', 'work_zone'])
            ->whereNotIn('role_id', [User::ROLE_MANAGER, User::ROLE_ADMIN]);

        if ($selectedChildId) {
            $query->where('work_zone_id', $selectedChildId);
        } elseif ($selectedParentId) {
            $childIds = WorkZone::where('parent_id', $selectedParentId)->pluck('id');
            $query->whereIn('work_zone_id', $childIds);
        }

        $users = $query->get();

        $groupedUsers = $users->groupBy(fn($user) => $user->work_zone?->name ?? 'Boshqalar');

        $days = Month::where('year', '=', $year)
            ->where('month_id', '=', $month_id)
            ->first()?->days;

        $month_name = Month::getMonth($month_id);

        return view('days.list', compact(
            'users',
            'days',
            'groupedUsers',
            'month_name',
            'month_id',
            'year',
            'parentWorkZones',
            'childWorkZones',
            'selectedParentId',
            'selectedChildId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function createday(Request $request, User $user): JsonResponse
    {

        $validated = $request->validate([
            'days' => 'required|integer|min:0',
        ]);

        $month = session('month') ?? (int) date('m');
        $year = session('year') ?? (int) date('Y');

        $record = EmployeeDays::updateOrCreate(
            [
                'user_id' => $user->id,
                'month_id' => $month,
                'year' => $year,
            ],
            [
                'days' => $validated['days'],
            ]
        );

        return response()->json(['success' => true, 'data' => $record]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function activity(Request $request)
    {
        $this->authorize('commission.activity');
        $month_id = $this->month;
        $year = $this->year;

        $work_zone_id = $request->query('work_zone_id');
        $child_work_zone_id = $request->query('child_work_zone_id');

        $users = User::with(['working_days', 'work_zone'])
            ->when($work_zone_id, function ($query) use ($work_zone_id) {
                $query->whereHas('work_zone.parent', function ($q) use ($work_zone_id) {
                    $q->where('id', $work_zone_id);
                });
            })
            ->when($child_work_zone_id, function ($query) use ($child_work_zone_id) {
                $query->whereHas('work_zone', function ($q) use ($child_work_zone_id) {
                    $q->where('id', $child_work_zone_id);
                });
            })
            ->whereNotIn('role_id', [User::ROLE_MANAGER, User::ROLE_ADMIN])
            ->get();

        $groupedUsers = $users->groupBy(fn($user) => $user->work_zone?->name ?? 'Boshqalar');

        $days = Month::where('year', '=', $year)->first()?->days;

        $month_name = Month::getMonth($month_id);

        $kpi = Kpi::find(11);

        return view('days.activity', compact('users', 'days', 'groupedUsers', 'month_name', 'month_id', 'year', 'kpi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function createActivity(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required',
            'year' => 'required|integer|min:2000|max:2100',
            'result' => 'required|min:0',
            'feedback' => 'nullable|string',
        ]);

        $userKpi = UserKpi::where('user_id', $user->id)->where('kpi_id', 11)->firstOrFail();

        $score = Score::updateOrCreate(
            [
                'user_kpi_id' => $userKpi->id,
                'type'    => 3,
            ],
            [
                'score'     => $validated['result'],
                'feedback'     => $validated['feedback'],
                'is_active' => true,
                'scored_by' => auth()->id(),
            ]
        );

        $userKpi->current_score = $score->score;
        $userKpi->score_id = $score->id;
        $userKpi->save();

        return response()->json(['success' => true, 'data' => $userKpi]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function behavior()
    {
        $month_id = session('month') ?? (int) date('m');
        $year = session('year') ?? (int) date('Y');

        $users = User::with(['working_days', 'work_zone'])->whereNotIn('role_id', [User::ROLE_MANAGER, User::ROLE_ADMIN])->get();

        $groupedUsers = $users->groupBy(fn($user) => $user->work_zone?->name ?? 'Boshqalar');

        $days = Month::where('year', '=', $year)->first()?->days;

        $month_name = Month::getMonth($month_id);

        $kpis = Kpi::where('type', Kpi::BEHAVIOUR)->whereNotNull('parent_id')->get();

        $title = 'Mehnat intizomiga rioya qilinganligi';

        return view('commission.users', compact('users', 'title', 'days', 'groupedUsers', 'month_name', 'month_id', 'year', 'kpis'));
    }
}
