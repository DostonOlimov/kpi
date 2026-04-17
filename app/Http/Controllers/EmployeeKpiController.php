<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\UserKpi;
use App\Models\WorkZone;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kpi;

class EmployeeKpiController extends Controller
{
    public function index(WorkZone $workZone, Request $request)
    {
        $workZoneId = $request->input('work_zone_id', $workZone->id);
        $workZone = WorkZone::findOrFail($workZoneId);
        $query = User::withCount(['kpis'])
            ->whereNotIn('role_id',[User::ROLE_ADMIN,User::ROLE_MANAGER])
            ->with('user_kpis', 'work_zone')
            ->whereIn('work_zone_id', function ($query) use ($workZone) {
                $query->select('id')
                    ->from('work_zones')
                    ->where('parent_id', $workZone->id);
            });

    // Filter by department if user is not admin
        if (auth()->user()->role_id == User::ROLE_DIRECTOR) {
            $query->where('work_zone_id', auth()->user()->work_zone_id)
                ->where('role_id', User::ROLE_USER);
        }

        // Apply search by name
        if ($request->filled('search')) {
            $query->orWhere('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('first_name', 'like', '%' . $request->search . '%');
        }

        // Apply department filter
        if ($request->filled('department_id')) {
            $query->where('work_zone_id', $request->department_id);
        }

        $users = $query->paginate(12)->withQueryString(); // Keep query in pagination

        return view('employee_kpis.index', compact('users','workZone'));
    }

    public function showKpis(User $user,Request $request)
    {

        $user->load('user_kpis');
        $work_zone_id = $user->work_zone?->parent_id;

        $query = Kpi::with('user_kpis')->where('user_id',$user->id);

        // Apply search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply department filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereHas('user_kpis');
            } elseif ($request->status === 'inactive') {
                $query->whereDoesntHave('user_kpis');
            }
        }

        $kpis = $query->get();

        return view('employee_kpis.kpis', compact('user', 'kpis', 'work_zone_id'));
    }

    public function toggle(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $kpi = Kpi::findOrFail($request->kpi_id); // Better than using where() + first()

        // Try to get the user_kpi record
        $userKpi = $user->user_kpis()->where('kpi_id', $kpi->id)->first();

        // If not exists, create it as active
        if (!$userKpi) {
            // Calculate new total if this KPI is added
            $currentTotal = $user->user_kpis()
                            ->whereHas('kpi', function($q) {
                                    $q->where('type', \App\Models\Kpi::SELF_BY_PERSON);
                                })->sum('target_score');
            $newTotal = $currentTotal + $kpi->max_score;

            if ($newTotal > 60) {
                return response()->json(['message' => 'Faol KPI ballari jami 60 dan oshmasligi kerak.'], 422);
            }

            $userKpi = $user->user_kpis()->create([
                'user_id' => $user->id,
                'kpi_id' => $kpi->id,
                'target_score' => $kpi->max_score,
                'year' => session('year') ?? (int)date('Y'),
                'month' => session('month') ?? (int)date('m'),
            ]);

            return response()->json([
                'is_active' => true,
                'total_active_score' => $newTotal
            ]);
        }

         else {
            // If deactivating and has no tasks, delete the record
            if ($userKpi->tasks()->count() == 0) {
                $userKpi->delete();
            }

            $newTotal = $user->user_kpis()->whereHas('kpi', function($q) {
                                    $q->where('type', \App\Models\Kpi::SELF_BY_PERSON);
                                })->sum('target_score');

            return response()->json([
                'is_active' => false,
                'total_active_score' => $newTotal
            ]);
        }
    }

    public function checkCompletion($kpiId)
    {
        try {
            $userKpi = UserKpi::with(['tasks'])->findOrFail($kpiId);

            $totalTasks = $userKpi->tasks->count();
            $scoredTasks = $userKpi->tasks->filter(function($task) {
                return $task->score !== null ;
            });

            $scoredTasksCount = $scoredTasks->count();
            $unscoredCount = $totalTasks - $scoredTasksCount;

            if ($unscoredCount > 0) {
                return response()->json([
                    'can_complete' => false,
                    'unscored_count' => $unscoredCount,
                    'total_tasks' => $totalTasks
                ]);
            }

            // Calculate average score
            $averageScore = $scoredTasks->avg(function($task) {
                return $task->score;
            });

            return response()->json([
                'can_complete' => true,
                'average_score' => round($averageScore, 2),
                'total_tasks' => $totalTasks,
                'max_score' => $userKpi->target_score
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Tekshirishda xatolik yuz berdi.'
            ], 500);
        }
    }

    /**
     * Complete or update KPI score
     */
    public function completeKpi(Request $request)
    {
        $request->validate([
            'kpi_id' => 'required|exists:user_kpis,id',
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000'
        ]);

        try {
            $userKpi = UserKpi::findOrFail($request->kpi_id);

            if ($request->score > $userKpi->target_score) {
                return response()->json(['message' => 'Ball maxsimal balldan oshmasligi kerak.'], 422);
            }

            $score = Score::create([
                'user_kpi_id' => $userKpi->id,
                'score' => $request->score,
                'type' => Score::SCORE_BY_DIRECTOR,
                'feedback' => $request->feedback,
                'scored_by' => auth()->user()->id
            ]);

            // Update KPI with final score and feedback
            $userKpi->update([
                'current_score' => $request->score,
                'score_id' => $score->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'KPI muvaffaqiyatli yakunlandi!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'KPI yakunlashda xatolik yuz berdi.'
            ], 500);
        }
    }

}
