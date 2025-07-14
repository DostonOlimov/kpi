<?php


namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\Task;
use App\Models\UserKpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeProfileController extends Controller
{
    public function index()
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
        $totalKPIs = UserKpi::where('user_id', $userId)->count();
        $totalTasks = Task::where('user_id', $userId)->count();

        $reviewedTasks = Task::where('user_id', $userId)
            ->where('is_checked',true)
            ->count();

        $kpiTasks = UserKpi::whereHas('tasks', function($taskQuery) use ($userId) {
            $taskQuery->where('user_id', $userId);
        })->count();

        $scoredKPIs = UserKpi::whereNotNull('current_score')->count();

        $kpis = UserKpi::whereNotNull('current_score')->sum('current_score');

        $completionRate = $totalTasks > 0 ? ($kpiTasks / $totalKPIs) * 100 : 0;
        $reviewRate = $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0;

        $scoringProgress = $totalKPIs > 0 ? ($scoredKPIs / $totalKPIs) * 100 : 0;


        return [
            'total_kpis' => $totalKPIs,
            'total_tasks' => $totalTasks,
            'reviewed_tasks' => $reviewedTasks,
            'scored_kpis' => $scoredKPIs,
            'average_score' => 1,
            'completion_rate' => round($completionRate, 1),
            'review_rate' => round($reviewRate, 1),
            'scoring_progress' => round($scoringProgress, 1)
        ];
    }

    public function create(Request $request)
    {
        $userId = auth()->id();

        // Get KPIs from user_kpis (KpiEmployees) for the current user
        $userKpis = UserKpi::where('user_id', $userId)
            ->with(['kpi','score','tasks'])
            ->get();

        return view('employee_profile.create', [
            'user_kpis' => $userKpis,
            'userId'=> $userId,
        ]);
    }
}
