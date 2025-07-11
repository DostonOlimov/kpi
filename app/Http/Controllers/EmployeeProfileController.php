<?php


namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\UserKpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeProfileController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        // Get user's KPIs from user_kpis (UserKpi) with related kpi
        $userKpis = UserKpi::with(['kpi.parent', 'kpi.children','tasks'])
            ->where('user_id', $userId)
            ->get();

        // Group KPIs by parent (category)
        $categories = [];
        foreach ($userKpis as $userKpi) {
            $kpi = $userKpi->kpi;
            if (!$kpi) continue;
            $parentId = $kpi->parent_id ?? $kpi->id;
            if (!isset($categories[$parentId])) {
                $categories[$parentId] = [
                    'category' => $kpi->parent ?? $kpi,
                    'children' => [],
                    'total_current_score' => 0,
                    'total_target_score' => 0,
                ];
            }
            $categories[$parentId]['children'][] = $kpi;
            $categories[$parentId]['total_current_score'] += $userKpi->current_score;
            $categories[$parentId]['total_target_score'] += $userKpi->target_score;
        }

        // Calculate average score for each category
        foreach ($categories as &$cat) {
            $cat['average_score'] = $cat['total_target_score'] > 0
                ? 100 * ($cat['total_current_score'] / $cat['total_target_score'])
                : null;
        }
        unset($cat);

        // Convert to collection for view compatibility
        $kpis = collect($categories);
        // foreach ($kpis as $kpi) {
        //     foreach ($kpi['children'] as &$child) {
        //         dd($child->tasks->where('user_id',$userId)->get());// Add score attribute to each child
        //     }
        // }


        // Calculate user statistics
        $userStats = $this->calculateUserStats($userId);

        return view('kpi_forms.list', compact('kpis', 'userStats','userId'));
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
            ->with(['kpi.parent', 'kpi.children','score','tasks'])
            ->get();

        // Extract the related KPIs
        $kpis = $userKpis->pluck('kpi')->filter();

        // Optionally, get unique parent KPIs
        $parentKpis = $kpis->pluck('parent')->filter()->unique('id')->values();

        return view('kpi_forms.create', [
            'user_kpis' => $userKpis,
            'kpis' => $kpis,
            'parent_kpis' => $parentKpis,
            'userId'=> $userId,
            'month' => session('month') ?? date('m'),
            'year' => session('year') ?? date('Y'),
        ]);
    }
}
