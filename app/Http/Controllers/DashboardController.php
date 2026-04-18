<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\Task;
use App\Models\User;
use App\Models\UserKpi;
use App\Models\WorkZone;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user && (int) $user->role_id === User::ROLE_USER) {
            return $this->userDashboard($user);
        }

        if ($user && (int) $user->role_id === User::ROLE_DIRECTOR) {
            return $this->directorDashboard($user);
        }

        $totalEmployees = User::whereNotIn('role_id', [User::ROLE_ADMIN,User::ROLE_MANAGER])->count();
        $totalTasks = Task::count();
        $completedKpis = UserKpi::where('status', UserKpi::STATUS_COMPLETED)->count();
        $totalKpis = UserKpi::count();
        $completionPercentage = $totalKpis > 0 ? round(($completedKpis / $totalKpis) * 100, 2) : 0;
        $recentTasks = Task::with('user_kpi.kpi')->latest()->take(5)->get();

        $kpiStatusData = [
            'new' => UserKpi::where('status', UserKpi::STATUS_NEW)->count(),
            'in_progress' => UserKpi::where('status', UserKpi::STATUS_IN_PROGRESS)->count(),
            'completed' => UserKpi::where('status', UserKpi::STATUS_COMPLETED)->count(),
        ];

        $monthlyData = $this->getMonthlyData();
        $regionResults = $this->getRootRegionResults();
        $focusSections = $regionResults->sortByDesc('progress')->take(6)->values();
        $topRegion = $regionResults->sortByDesc('current_score')->first();

        return view('dashboard.dashboard', compact(
            'totalEmployees',
            'totalTasks',
            'completedKpis',
            'completionPercentage',
            'recentTasks',
            'kpiStatusData',
            'monthlyData',
            'regionResults',
            'focusSections',
            'topRegion'
        ));
    }

    private function userDashboard(User $user)
    {
        $userKpis = UserKpi::with(['kpi', 'tasks'])
            ->where('user_id', $user->id)
            ->whereHas('kpi',function ($query){$query->where('status','!=', Kpi::PERMANENT);})
            ->get();

        $assignedKpis = $userKpis->count();
        $completedKpis = $userKpis->where('status', UserKpi::STATUS_COMPLETED)->count();
        $inProgressKpis = $userKpis->where('status', UserKpi::STATUS_IN_PROGRESS)->count();
        $newKpis = $userKpis->where('status', UserKpi::STATUS_NEW)->count();

        $targetScore = (float) $userKpis->sum('target_score');
        $currentScore = (float) $userKpis->sum('current_score');
        $completionRate = $assignedKpis > 0 ? round(($completedKpis / $assignedKpis) * 100) : 0;
        $scoreRate = $targetScore > 0 ? round(($currentScore / $targetScore) * 100) : 0;

        $tasks = Task::with(['user_kpi.kpi'])
            ->whereHas('user_kpi', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        $recentTasks = $tasks->take(5);
        $totalTasks = $tasks->count();
        $scoredTasks = $tasks->whereNotNull('score')->count();
        $pendingTasks = max($totalTasks - $scoredTasks, 0);
        $averageTaskScore = $scoredTasks > 0 ? round((float) $tasks->whereNotNull('score')->avg('score'), 1) : 0;

        $statusSegments = [
            ['label' => 'Yangi', 'count' => $newKpis, 'color' => '#f97316'],
            ['label' => 'Jarayonda', 'count' => $inProgressKpis, 'color' => '#2563eb'],
            ['label' => 'Bajarilgan', 'count' => $completedKpis, 'color' => '#10b981'],
        ];

        $personalMonthlyData = $this->getPersonalMonthlyData($user);
        $focusKpis = $userKpis
            ->sortByDesc(function ($item) {
                return ($item->current_score ?? 0) - ($item->target_score ?? 0);
            })
            ->take(4)
            ->values()
            ->map(function ($item) {
                $target = max((float) $item->target_score, 0);
                $current = max((float) $item->current_score, 0);
                $progress = $target > 0 ? min(100, round(($current / $target) * 100)) : 0;

                return [
                    'name' => $item->kpi->name ?? 'Noma\'lum KPI',
                    'target_score' => $target,
                    'current_score' => $current,
                    'progress' => $progress,
                    'status' => $item->status_name,
                    'task_count' => $item->tasks->count(),
                ];
            });

        $performanceBands = [
            ['label' => 'Ball bajarilishi', 'value' => $scoreRate, 'color' => '#0f766e'],
            ['label' => 'KPI yopilishi', 'value' => $completionRate, 'color' => '#1d4ed8'],
            ['label' => 'Vazifa baholanishi', 'value' => $totalTasks > 0 ? round(($scoredTasks / $totalTasks) * 100) : 0, 'color' => '#7c3aed'],
        ];

        return view('dashboard.user-dashboard', compact(
            'user',
            'assignedKpis',
            'completedKpis',
            'inProgressKpis',
            'newKpis',
            'targetScore',
            'currentScore',
            'completionRate',
            'scoreRate',
            'totalTasks',
            'scoredTasks',
            'pendingTasks',
            'averageTaskScore',
            'recentTasks',
            'statusSegments',
            'personalMonthlyData',
            'focusKpis',
            'performanceBands'
        ));
    }

    private function directorDashboard(User $director)
    {
        $employees = User::where('role_id', User::ROLE_USER)
            ->where('work_zone_id', $director->work_zone_id)
            ->get();

        $employeeIds = $employees->pluck('id');

        $userKpis = UserKpi::with(['kpi', 'tasks', 'user'])
            ->whereIn('user_id', $employeeIds)
            ->whereHas('kpi',function ($query){$query->where('status','!=', Kpi::PERMANENT);})
            ->get();

        $assignedKpis = $userKpis->count();
        $completedKpis = $userKpis->where('status', UserKpi::STATUS_COMPLETED)->count();
        $inProgressKpis = $userKpis->where('status', UserKpi::STATUS_IN_PROGRESS)->count();
        $newKpis = $userKpis->where('status', UserKpi::STATUS_NEW)->count();

        $targetScore = (float) $userKpis->sum('target_score');
        $currentScore = (float) $userKpis->sum('current_score');
        $completionRate = $assignedKpis > 0 ? round(($completedKpis / $assignedKpis) * 100) : 0;
        $scoreRate = $targetScore > 0 ? round(($currentScore / $targetScore) * 100) : 0;

        $tasks = Task::with(['user_kpi.kpi', 'user_kpi.user'])
            ->whereHas('user_kpi', function ($query) use ($employeeIds) {
                $query->whereIn('user_id', $employeeIds);
            })
            ->latest()
            ->get();

        $recentTasks = $tasks->take(5);
        $totalTasks = $tasks->count();
        $scoredTasks = $tasks->whereNotNull('score')->count();
        $pendingTasks = max($totalTasks - $scoredTasks, 0);
        $averageTaskScore = $scoredTasks > 0 ? round((float) $tasks->whereNotNull('score')->avg('score'), 1) : 0;

        $statusSegments = [
            ['label' => 'Yangi', 'count' => $newKpis, 'color' => '#f97316'],
            ['label' => 'Jarayonda', 'count' => $inProgressKpis, 'color' => '#2563eb'],
            ['label' => 'Bajarilgan', 'count' => $completedKpis, 'color' => '#10b981'],
        ];

        $personalMonthlyData = $this->getDepartmentMonthlyData($employeeIds);
        $focusKpis = $employees
            ->map(function ($employee) use ($userKpis) {
                $employeeKpis = $userKpis->where('user_id', $employee->id);
                $target = (float) $employeeKpis->sum('target_score');
                $current = (float) $employeeKpis->sum('current_score');
                $progress = $target > 0 ? min(100, round(($current / $target) * 100)) : 0;

                return [
                    'name' => $employee->full_name,
                    'target_score' => $target,
                    'current_score' => $current,
                    'progress' => $progress,
                    'status' => $progress >= 80 ? 'Yuqori natija' : ($progress >= 50 ? 'Jarayonda' : 'Etibor kerak'),
                    'task_count' => $employeeKpis->sum(function ($item) {
                        return $item->tasks->count();
                    }),
                ];
            })
            ->sortByDesc('progress')
            ->take(4)
            ->values();

        $performanceBands = [
            ['label' => 'Ball bajarilishi', 'value' => $scoreRate, 'color' => '#0f766e'],
            ['label' => 'KPI yopilishi', 'value' => $completionRate, 'color' => '#1d4ed8'],
            ['label' => 'Vazifa baholanishi', 'value' => $totalTasks > 0 ? round(($scoredTasks / $totalTasks) * 100) : 0, 'color' => '#7c3aed'],
        ];

        $teamSize = $employees->count();
        $sectionResults = $this->getDirectorSectionResults($director);

        return view('dashboard.director-dashboard', compact(
            'director',
            'teamSize',
            'sectionResults',
            'assignedKpis',
            'completedKpis',
            'inProgressKpis',
            'newKpis',
            'targetScore',
            'currentScore',
            'completionRate',
            'scoreRate',
            'totalTasks',
            'scoredTasks',
            'pendingTasks',
            'averageTaskScore',
            'recentTasks',
            'statusSegments',
            'personalMonthlyData',
            'focusKpis',
            'performanceBands'
        ));
    }

    private function getMonthlyData()
    {
        $data = [];
        $current = Carbon::now()->startOfMonth();

        for ($i = 5; $i >= 0; $i--) {
            $date = $current->copy()->subMonths($i);
            $month = (int) $date->month;
            $year = (int) $date->year;

            $completed = UserKpi::withoutGlobalScopes()
                ->where('month', $month)
                ->where('year', $year)
                ->where('status', UserKpi::STATUS_COMPLETED)
                ->count();

            $total = UserKpi::withoutGlobalScopes()
                ->where('month', $month)
                ->where('year', $year)
                ->count();

            $data[] = [
                'month' => $date->format('M'),
                'completed' => $completed,
                'total' => $total,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            ];
        }

        return $data;
    }

    private function getPersonalMonthlyData(User $user)
    {
        $data = [];
        $current = Carbon::now()->startOfMonth();

        for ($i = 5; $i >= 0; $i--) {
            $date = $current->copy()->subMonths($i);
            $month = (int) $date->month;
            $year = (int) $date->year;

            $monthKpis = UserKpi::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            $target = (float) $monthKpis->sum('target_score');
            $currentScore = (float) $monthKpis->sum('current_score');

            $data[] = [
                'month' => $date->format('M'),
                'target' => $target,
                'current' => $currentScore,
                'rate' => $target > 0 ? round(($currentScore / $target) * 100) : 0,
            ];
        }

        return $data;
    }

    private function getDepartmentMonthlyData($employeeIds)
    {
        $data = [];
        $current = Carbon::now()->startOfMonth();

        for ($i = 5; $i >= 0; $i--) {
            $date = $current->copy()->subMonths($i);
            $month = (int) $date->month;
            $year = (int) $date->year;

            $monthKpis = UserKpi::withoutGlobalScopes()
                ->whereIn('user_id', $employeeIds)
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            $target = (float) $monthKpis->sum('target_score');
            $currentScore = (float) $monthKpis->sum('current_score');

            $data[] = [
                'month' => $date->format('M'),
                'target' => $target,
                'current' => $currentScore,
                'rate' => $target > 0 ? round(($currentScore / $target) * 100) : 0,
            ];
        }

        return $data;
    }

    private function getDirectorSectionResults(User $director)
    {
        $sections = WorkZone::where('parent_id', $director->work_zone_id)->get();

        if ($sections->isEmpty()) {
            $currentSection = WorkZone::find($director->work_zone_id);
            $sections = collect($currentSection ? [$currentSection] : []);
        }

        return $sections->map(function ($section) {
            $employeeIds = User::where('role_id', User::ROLE_USER)
                ->where('work_zone_id', $section->id)
                ->pluck('id');

            $kpis = UserKpi::with('tasks')
                ->whereIn('user_id', $employeeIds)
                ->get();

            $target = (float) $kpis->sum('target_score');
            $current = (float) $kpis->sum('current_score');
            $completed = $kpis->where('status', UserKpi::STATUS_COMPLETED)->count();
            $total = $kpis->count();
            $taskCount = $kpis->sum(function ($item) {
                return $item->tasks->count();
            });

            return [
                'name' => $section->name,
                'employee_count' => $employeeIds->count(),
                'target_score' => $target,
                'current_score' => $current,
                'completed_kpis' => $completed,
                'total_kpis' => $total,
                'task_count' => $taskCount,
                'progress' => $target > 0 ? round(($current / $target) * 100) : 0,
            ];
        })->values();
    }

    private function getRootRegionResults()
    {
        $regions = WorkZone::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return $regions->map(function ($region) {
            $sectionIds = WorkZone::where('parent_id', $region->id)->pluck('id');
            $zoneIds = $sectionIds->isEmpty()
                ? collect([$region->id])
                : $sectionIds->prepend($region->id)->unique()->values();

            $employees = User::where('role_id', User::ROLE_USER)
                ->whereIn('work_zone_id', $zoneIds)
                ->get();

            $employeeIds = $employees->pluck('id');

            $kpis = UserKpi::with('tasks')
                ->whereIn('user_id', $employeeIds)
                ->get();

            $targetScore = (float) $kpis->sum('target_score');
            $currentScore = (float) $kpis->sum('current_score');
            $completedKpis = $kpis->where('status', UserKpi::STATUS_COMPLETED)->count();
            $totalKpis = $kpis->count();
            $taskCount = $kpis->sum(function ($item) {
                return $item->tasks->count();
            });

            return [
                'id' => $region->id,
                'name' => $region->name,
                'employee_count' => $employees->count(),
                'section_count' => max($zoneIds->count() - 1, 0),
                'target_score' => $targetScore,
                'current_score' => $currentScore,
                'completed_kpis' => $completedKpis,
                'total_kpis' => $totalKpis,
                'task_count' => $taskCount,
                'progress' => $targetScore > 0 ? round(($currentScore / $targetScore) * 100) : 0,
            ];
        })->values();
    }
}
