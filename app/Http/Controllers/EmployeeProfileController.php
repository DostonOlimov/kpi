<?php


namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\Task;
use App\Models\UserKpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeProfileController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        // Get all parent KPIs with their children
        $parentKpis = Kpi::whereNull('parent_id')
            ->with([
                'user_kpis' => function($query) use ($userId) {
                    $query->where('user_id', $userId)->with('score');
                },
                'children'
            ])
            ->where('type','!=',Kpi::PERMANENT)
            ->orderBy('type')
            ->get();


        // Get user's KPIs (only child KPIs)
        $userKpis = UserKpi::where('user_id', $userId)
            ->with(['kpi', 'score'])
            ->get();
        $parentKpiIds = $userKpis
            ->filter(fn($userKpi) => $userKpi->kpi && $userKpi->kpi->parent_id !== null)
            ->pluck('kpi.parent_id')
            ->unique()
            ->values();

        $parentKpis = Kpi::whereIn('id', $parentKpiIds)->get();

        // Calculate statistics
        $totalKpis = $userKpis->count();
        $completedKpis = $userKpis->whereNotNull('score_id')->count();
        $totalCurrentScore = $userKpis->sum('current_score');
        $totalTargetScore = $userKpis->sum('target_score');
        $totalMaxScore = $userKpis->sum(fn($userKpi) => $userKpi->kpi->max_score);

        return view('employee_profile.dashboard', compact(
            'parentKpis',
            'userKpis',
            'totalKpis',
            'completedKpis',
            'totalCurrentScore',
            'totalTargetScore',
            'totalMaxScore',
            'userId'
        ));
    }

    public function show($kpiId)
    {
        $userId = Auth::id();

        $userKpi = UserKpi::where('user_id', $userId)
            ->where('kpi_id', $kpiId)
            ->with(['kpi.parent', 'score'])
            ->first();

        if (!$userKpi) {
            abort(404);
        }

        return view('employee_profile.detail', compact('userKpi'));
    }
    public function index2()
    {
        $userId = Auth::id();

        // Get user's KPIs with tasks and scores
        $constant_kpis = Kpi::with([
                'children' => function($query) use ($userId) {
                }
            ])
            ->whereNull('parent_id')
            ->where('type','!=',Kpi::SELF_BY_PERSON)
            ->get();

        // Get KPIs from user_kpis (KpiEmployees) for the current user
        $userKpis = UserKpi::where('user_id', $userId)
            ->with(['kpi.parent', 'kpi.children','score','tasks'])
            ->get();

        // Extract the related KPIs
        $kpis = $userKpis->pluck('kpi')->filter();

        // Optionally, get unique parent KPIs
        $parentKpis = $kpis->pluck('parent')->filter()->unique('id')->values();


        // Calculate user statistics
        $userStats = $this->calculateUserStats($userId);

        return view('employee_profile.list', compact('kpis', 'userStats','parentKpis','userKpis','constant_kpis'));
    }


    private function calculateUserStats($userId)
    {
        // Eager load tasks to avoid N+1 problem
        $userKpis = UserKpi::with('tasks')->where('user_id', $userId)->get();

        $totalKPIs = $userKpis->count();
        $totalTasks = 0;
        $reviewedTasks = 0;
        $scoredKPIs = 0;
        $totalScore = 0;

        foreach ($userKpis as $userKpi) {
            $tasks = $userKpi->tasks;
            $taskCount = $tasks->count();
            $reviewedCount = $tasks->whereNotNull('score')->count();

            $totalTasks += $taskCount;
            $reviewedTasks += $reviewedCount;

            if (!is_null($userKpi->current_score)) {
                $scoredKPIs++;
                $totalScore += $userKpi->current_score;
            }
        }

        $completionRate = $totalKPIs > 0 ? ($totalTasks / $totalKPIs) * 100 : 0;
        $reviewRate = $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0;
        $scoringProgress = $totalKPIs > 0 ? ($scoredKPIs / $totalKPIs) * 100 : 0;
        $averageScore = $scoredKPIs > 0 ? round($totalScore / $scoredKPIs, 1) : 0;

        return [
            'total_kpis' => $totalKPIs,
            'total_tasks' => $totalTasks,
            'reviewed_tasks' => $reviewedTasks,
            'scored_kpis' => $scoredKPIs,
            'average_score' => $averageScore,
            'completion_rate' => round($completionRate, 1),
            'review_rate' => round($reviewRate, 1),
            'scoring_progress' => round($scoringProgress, 1),
        ];
    }


    public function create(Request $request)
    {
        $userId = auth()->id();

        $userKpis = UserKpi::where('user_id', $userId)
            ->whereHas('kpi', function ($query) {
                $query->where('type', Kpi::SELF_BY_PERSON);
            })
            ->with(['kpi', 'score', 'tasks'])
            ->get();


        return view('employee_profile.create', [
            'user_kpis' => $userKpis,
            'userId'=> $userId,
        ]);
    }
}
