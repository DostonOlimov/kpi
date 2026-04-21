<?php


namespace App\Http\Controllers;

use App\Models\EmployeeKpiResult;
use App\Models\UserKpi;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use app\Models\User;
use App\Models\WorkZone;

class KpiResultsController extends Controller
{
    /**
     * Display employee KPI results list.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function kpiResults(Request $request)
    {
        $year = session('year') ?? date('Y');
        $month = session('month') ?? date('m');
        $work_zone_id = $request->input('work_zone_id',32);
        $child_work_zone_id = $request->input('child_work_zone_id');
        
        $results = EmployeeKpiResult::with(['user.work_zone', 'user.role', 'evaluator'])
            ->where('year', $year)
            ->where('month', $month)
            ->whereHas('user', function($query) use ($work_zone_id, $child_work_zone_id) {
                $query->whereHas('work_zone', function($query) use ($work_zone_id, $child_work_zone_id) {
                    $query->where('parent_id', $work_zone_id);
                    if($child_work_zone_id){
                        $query->where('id', $child_work_zone_id);
                    }
                })->whereNotIn('role_id', [User::ROLE_ADMIN, User::ROLE_MANAGER]);
            })
            ->latest()
            ->paginate(20);
        
        return view('employees.kpi-results', compact('results', 'work_zone_id', 'child_work_zone_id', 'year', 'month'));
    }

    /**
     * Calculate/refresh KPI results for all employees in work zone.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function calculateResults(WorkZone $workZone, Request $request)
    {
        $year = session('year') ?? date('Y');
        $month = session('month') ?? date('m');

        // Get all employees in this work zone
        $employees = User::where('work_zone_id', $workZone->id)
            ->whereNotIn('role_id', [User::ROLE_ADMIN, User::ROLE_MANAGER])
            ->get();
        
        $calculated = 0;
        
        foreach ($employees as $employee) {
            // Get all user KPIs for this employee
            $userKpis = UserKpi::where('user_id', $employee->id)
                ->where('year', $year)
                ->where('month', $month)
                ->with('kpi')
                ->get();
            
            if ($userKpis->isEmpty()) {
                continue;
            }
            
            // Calculate total score (sum of current_scores)
            $totalScore = $userKpis->sum('current_score');
            
            // Get or create result record
            $result = EmployeeKpiResult::updateOrCreate(
                [
                    'user_id' => $employee->id,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'total_score' => $totalScore,
                    'final_score' => $totalScore, // Can be adjusted later
                    'status' => EmployeeKpiResult::STATUS_CALCULATED,
                ]
            );
            
            // Determine grade based on score
            $grade = $this->calculateGrade($totalScore);
            $result->update(['grade' => $grade]);
            
            $calculated++;
        }
        
        return redirect()->back()
            ->with('message', "{$calculated} xodim uchun KPI natijalari hisoblandi.");
    }

    /**
     * Calculate grade based on total score.
     *
     * @param float $score
     * @return string
     */
    private function calculateGrade($score)
    {
        if ($score >= 90) {
            return 'A';
        } elseif ($score >= 75) {
            return 'B';
        } elseif ($score >= 60) {
            return 'C';
        } elseif ($score >= 40) {
            return 'D';
        } else {
            return 'F';
        }
    }
}
