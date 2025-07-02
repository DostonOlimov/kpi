<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\KpiScore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\EmployeeDays;
use App\Models\Month;
use App\Models\User;

class EmployeeDaysController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function list()
    {
        $month_id = session('month') ?? (int) date('m');
        $year = session('year') ?? (int) date('Y');

        $users = User::with(['working_days', 'work_zone'])->get();

        $groupedUsers = $users->groupBy(fn($user) => $user->work_zone?->name ?? 'Boshqalar');

        $days = Month::where('year','=',$year)->first()?->days;

        $month_name = Month::getMonth($month_id);

        return view('days.list', compact('users','days','groupedUsers','month_name','month_id','year',));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function createday(Request $request,User $user): JsonResponse
    {

        $validated = $request->validate([
            'month' => 'required',
            'year' => 'required|integer|min:2000|max:2100',
            'days' => 'required|integer|min:0',
        ]);

        $days = Month::where('year','=',$validated['year'])->first()?->days;

        $record = EmployeeDays::updateOrCreate([
            'user_id'=>$user->id,
            'month_id' => $validated['month'],
            'year' => $validated['year'],
        ],
            [
            'days' => $validated['days'],
        ]);

        KpiScore::updateOrCreate(
            [
                'kpi_id' => 8,
                'user_id' => $user->id,
                'type'    => 2,
                'month'     => session('month') ?? (int)date('m'),
                'year'      => session('year') ?? (int)date('Y'),
            ],
            [
                'score'     => round(($validated['days'] / $days) * 7,2),
                'is_active' => true,
                'scored_by' => auth()->id(),
                'scored_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'data' => $record]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function activity()
    {
        $month_id = session('month') ?? (int) date('m');
        $year = session('year') ?? (int) date('Y');

        $users = User::with(['scores', 'work_zone'])->get();

        $groupedUsers = $users->groupBy(fn($user) => $user->work_zone?->name ?? 'Boshqalar');

        $month_name = Month::getMonth($month_id);

        $kpi = Kpi::findOrFail(11);

        return view('days.activity', compact('users','groupedUsers','month_name','month_id','year','kpi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function createActivity(Request $request,User $user): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required',
            'year' => 'required|integer|min:2000|max:2100',
            'result' => 'required|min:0',
            'feedback' => 'nullable|string',
        ]);

        $kpi = KpiScore::updateOrCreate(
            [
                'kpi_id' => 11,
                'user_id' => $user->id,
                'type'    => 3,
                'month'     => $validated['month'],
                'year'      => $validated['year'],
            ],
            [
                'score'     => $validated['result'],
                'feedback'     => $validated['feedback'],
                'is_active' => true,
                'scored_by' => auth()->id(),
                'scored_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'data' => $kpi]);
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

        $users = User::with(['scores', 'work_zone'])->get();

        $groupedUsers = $users->groupBy(fn($user) => $user->work_zone?->name ?? 'Boshqalar');

        $month_name = Month::getMonth($month_id);

        $kpi = Kpi::findOrFail(9);

        return view('days.behavior', compact('users','kpi','groupedUsers','month_name','month_id','year',));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function createBehavior(Request $request,User $user): JsonResponse
    {

        $validated = $request->validate([
            'month' => 'required',
            'year' => 'required|integer|min:2000|max:2100',
            'result' => 'required|min:0',
            'feedback' => 'nullable|string',
        ]);

        $kpi = KpiScore::updateOrCreate(
            [
                'kpi_id' => 9,
                'user_id' => $user->id,
                'type'    => 3,
                'month'     => $validated['month'],
                'year'      => $validated['year'],
            ],
            [
                'score'     => $validated['result'],
                'feedback'     => $validated['feedback'],
                'is_active' => true,
                'scored_by' => auth()->id(),
                'scored_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'data' => $kpi]);
    }

}
