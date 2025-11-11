<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserKpi;
use App\Models\Task;
use App\Models\Kpi;


class DashboardController extends Controller
{

    public function index()
    {
        // Get total number of employees
        $totalEmployees = User::where('role_id', '!=', User::ROLE_ADMIN)->count();
        
        // Get total number of tasks
        $totalTasks = Task::count();
        
        // Get total completed KPIs
        $completedKpis = UserKpi::where('status', UserKpi::STATUS_COMPLETED)->count();
        
        // Get total KPIs
        $totalKpis = UserKpi::count();
        
        // Calculate completion percentage
        $completionPercentage = $totalKpis > 0 ? round(($completedKpis / $totalKpis) * 100, 2) : 0;
        
        // Get recent tasks (last 5)
        $recentTasks = Task::with('user_kpi.kpi')->latest()->take(5)->get();
        
        // Get KPIs by status
        $kpiStatusData = [
            'new' => UserKpi::where('status', UserKpi::STATUS_NEW)->count(),
            'in_progress' => UserKpi::where('status', UserKpi::STATUS_IN_PROGRESS)->count(),
            'completed' => UserKpi::where('status', UserKpi::STATUS_COMPLETED)->count(),
        ];
        
        // Get data for the last 6 months
        $monthlyData = $this->getMonthlyData();
        
        return view('dashboard.dashboard', compact(
            'totalEmployees', 
            'totalTasks', 
            'completedKpis', 
            'completionPercentage', 
            'recentTasks', 
            'kpiStatusData',
            'monthlyData'
        ));
    }
    
    private function getMonthlyData()
    {
        $data = [];
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        // Get data for the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = $currentMonth - $i;
            $year = $currentYear;
            
            if ($month <= 0) {
                $month += 12;
                $year -= 1;
            }
            
            $completed = UserKpi::where('month', $month)
                ->where('year', $year)
                ->where('status', UserKpi::STATUS_COMPLETED)
                ->count();
                
            $total = UserKpi::where('month', $month)
                ->where('year', $year)
                ->count();
                
            $data[] = [
                'month' => date('M', mktime(0, 0, 0, $month, 10)),
                'completed' => $completed,
                'total' => $total,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0
            ];
        }
        
        return $data;
    }

}