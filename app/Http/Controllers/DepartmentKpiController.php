<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserKpi;
use App\Models\WorkZone;

class DepartmentKpiController extends Controller
{
    public function index()
    {
        // Get department statistics
        $departmentStats = $this->getDepartmentStatistics();
        
        // Get top performers by department
        $topPerformers = $this->getTopPerformersByDepartment();
        
        // Get KPI trends (last 6 months)
        $kpiTrends = $this->getKpiTrends();
        
        // Get overall statistics
        $overallStats = $this->getOverallStatistics();

        return view('department.department-statistics', compact(
            'departmentStats',
            'topPerformers', 
            'kpiTrends',
            'overallStats'
        ));
    }

    private function getDepartmentStatistics()
    {
        return WorkZone::leftJoin('users', 'work_zones.id', '=', 'users.work_zone_id')
            ->leftJoin('user_kpis', 'users.id', '=', 'user_kpis.user_id')
            ->select(
                'work_zones.id',
                'work_zones.name as department_name',
                DB::raw('COUNT(DISTINCT users.id) as total_employees'),
                DB::raw('COUNT(user_kpis.id) as total_kpis'),
                DB::raw('ROUND(AVG(user_kpis.current_score), 2) as avg_score'),
                DB::raw('MAX(user_kpis.current_score) as max_score'),
                DB::raw('MIN(user_kpis.current_score) as min_score'),
                DB::raw('COUNT(CASE WHEN user_kpis.current_score >= 80 THEN 1 END) as high_performers'),
                DB::raw('COUNT(CASE WHEN user_kpis.current_score < 60 THEN 1 END) as low_performers')
            )
            ->groupBy('work_zones.id', 'work_zones.name')
            ->orderBy('avg_score', 'desc')
            ->get();
    }

    private function getTopPerformersByDepartment()
    {
        return User::join('work_zones', 'users.work_zone_id', '=', 'work_zones.id')
            ->join('user_kpis', 'users.id', '=', 'user_kpis.user_id')
            ->select(
                'work_zones.name as department_name',
                'users.first_name as user_name',
                'users.last_name as email',
                DB::raw('ROUND(AVG(user_kpis.current_score), 2) as avg_score')
            )
            ->groupBy('work_zones.id', 'work_zones.name', 'users.id', 'user_name', 'email')
            ->havingRaw('AVG(user_kpis.current_score) >= 80')
            ->orderBy('work_zones.name')
            ->orderBy('avg_score', 'desc')
            ->get()
            ->groupBy('department_name');
    }

    private function getKpiTrends()
    {
        return UserKpi::join('users', 'user_kpis.user_id', '=', 'users.id')
            ->join('work_zones', 'users.work_zone_id', '=', 'work_zones.id')
            ->select(
                'work_zones.name as department_name',
                DB::raw('DATE_FORMAT(user_kpis.created_at, "%Y-%m") as month'),
                DB::raw('ROUND(AVG(user_kpis.current_score), 2) as avg_score')
            )
            ->where('user_kpis.created_at', '>=', now()->subMonths(6))
            ->groupBy('work_zones.id', 'work_zones.name', DB::raw('DATE_FORMAT(user_kpis.created_at, "%Y-%m")'))
            ->orderBy('month')
            ->get()
            ->groupBy('department_name');
    }

    private function getOverallStatistics()
    {
        return [
            'total_departments' => WorkZone::count(),
            'total_employees' => User::count(),
            'total_kpis' => UserKpi::count(),
            'overall_avg_score' => round(UserKpi::avg('current_score'), 2),
            'high_performers_count' => UserKpi::where('current_score', '>=', 80)->count(),
            'low_performers_count' => UserKpi::where('current_score', '<', 60)->count(),
        ];
    }

    public function departmentDetail($id)
    {
        $department = WorkZone::findOrFail($id);
        
        $employees = User::where('work_zone_id', $id)
            ->whereNotIn('role_id', [User::ROLE_ADMIN, User::ROLE_MANAGER])
            ->with(['user_kpis' => function($query) {
                $query->select('user_id', DB::raw('ROUND(AVG(current_score), 2) as avg_score'))
                      ->groupBy('user_id');
            }])
            ->get();

        return view('department.department-detail', compact('department', 'employees'));
    }

    public function usersShow($userId)
    {
        $user = User::with(['work_zone'])->findOrFail($userId);
        
        // Get parent KPIs with their children and user scores
        $parentKpis = Kpi::whereNull('parent_id')
            ->where('type','!=', Kpi::PERMANENT) // Exclude commission-based KPIs
            ->with([
                'user_kpis' => function($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->with(['user_kpis.tasks']);
                },
                'children'
            ])->orderBy('sort')
            ->get();

        // User statistics
        $userStats = $this->getUserStatistics($userId);
        
        // Performance trends (last 6 months)
        $performanceTrends = $this->getUserPerformanceTrends($userId);

        $userKpis = UserKpi::with(['kpi', 'tasks'])->where('user_id', $userId)->get();

        return view('department.user-detail', compact('user', 'parentKpis', 'userStats', 'performanceTrends','userKpis'));
    }

    private function getUserStatistics($userId)
    {
        $userKpis = UserKpi::where('user_id', $userId);
        
        $totalKpis = $userKpis->count();
        $avgScore = $userKpis->avg('current_score') ?? 0;
        $completedKpis = $userKpis->whereNotNull('current_score')->count();
        $inProgressKpis = $userKpis->whereNull('current_score')->count();
        $pendingKpis = $userKpis->whereNull('current_score')->count();
        
        // Commission-based KPIs
        $commissionKpis = $userKpis->whereHas('kpi', function($query) {
            $query->where('type', 1);
        });
        
        // Task-based KPIs
        $taskKpis = $userKpis->whereHas('kpi', function($query) {
            $query->where('type', 2);
        });

        return [
            'total_kpis' => $totalKpis,
            'avg_score' => round($avgScore, 2),
            'completed_kpis' => $completedKpis,
            'in_progress_kpis' => $inProgressKpis,
            'pending_kpis' => $pendingKpis,
            'commission_kpis_count' => $commissionKpis->count(),
            'task_kpis_count' => $taskKpis->count(),
            'total_commission' => $commissionKpis->sum('current_score'),
            'completion_rate' => $totalKpis > 0 ? round(($completedKpis / $totalKpis) * 100, 2) : 0
        ];
    }

    private function getUserPerformanceTrends($userId)
    {
        return UserKpi::where('user_id', $userId)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, AVG(current_score) as avg_score')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
