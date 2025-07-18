<?php


namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\Task;
use App\Models\User;
use App\Models\UserKpi;
use App\Models\WorkZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectorProfileController extends Controller
{
    public function index(Request $request)
    {

        $user = auth()->user();
        $users = User::with('user_kpis' )
            ->where('work_zone_id','=',$user->work_zone_id)
            ->where('role_id','=',User::ROLE_USER)
            ->get();

       return view('director.employees', ["users" => $users]);

    }

    public function check_user(int $type, User $employee, Request $request)
    {
        $userKpis = UserKpi::where('user_id', $employee->id)
            ->whereHas('kpi', function ($query) use ($type) {
                $query->where('type', Kpi::SELF_BY_PERSON);
            })
            ->with(['kpi.parent', 'kpi.children', 'tasks']) // eager load tasks too
            ->get();

        $reviewedTasks = $this->countReviewedTasks($userKpis);

        return view('director.checking', [
            'user_kpis' => $userKpis,
            'user' => $employee,
            'type' => $type,
            'reviewed_tasks' => $reviewedTasks,
        ]);
    }

    public function stats(Request $request)
    {
        $userId = $request->get('user_id');

        if (!$userId || !is_numeric($userId)) {
            return response()->json(['error' => 'Invalid user_id'], 400);
        }

        $userKpis = UserKpi::where('user_id', $userId)
            ->with('tasks') // eager load tasks
            ->get();

        $reviewedTasks = $this->countReviewedTasks($userKpis);

        return response()->json([
            'reviewed_tasks' => $reviewedTasks,
            'total_score' => $userKpis->sum('current_score'),
            'scored_kpis' => $userKpis->whereNotNull('current_score')->count()
        ]);
    }

    private function countReviewedTasks($userKpis): int
    {
        return $userKpis->sum(fn($userKpi) =>
        $userKpi->tasks->whereNotNull('score')->count()
        );
    }

    public function employees($departmentId = null)
    {
        // If no department ID provided, use current user's department
        if (!$departmentId) {
            $departmentId = Auth::user()->work_zone_id;
        }

        $department = WorkZone::with(['users', 'kpis'])->findOrFail($departmentId);

        // Calculate department statistics
        $departmentStats = $this->calculateDepartmentStats($department);

        // Get department users with their performance data
        $departmentUsers = $this->getDepartmentUsersPerformance($department);

        // Get Kpis with department-specific data
        $kpis = $this->getDepartmentKpis($department);

        return view('director.section', compact(
            'department',
            'departmentStats',
            'departmentUsers',
            'kpis'
        ));
    }

    public function overview()
    {
        // Get all departments with performance data
        $departments = WorkZone::with(['users', 'tasks'])
            ->withCount(['users', 'tasks'])
            ->get()
            ->map(function ($department) {
                return $this->enrichDepartmentData($department);
            })
            ->sortByDesc('average_score')
            ->values();

        // Calculate company-wide statistics
        $companyStats = $this->calculateCompanyStats();

        return view('departments.overview', compact('departments', 'companyStats'));
    }

    public function userDetails($userId)
    {
        $user = User::with(['tasks.comments', 'department'])->findOrFail($userId);

        // Calculate user statistics
        $userStats = $this->calculateUserDetailedStats($user);

        return view('department.user-details', compact('user', 'userStats'));
    }

    public function compare($departmentId)
    {
        $department = WorkZone::findOrFail($departmentId);

        // Get comparison data with other departments
        $comparisonData = $this->getDepartmentComparison($department);

        return view('departments.comparison', compact('department', 'comparisonData'));
    }

    private function calculateDepartmentStats($department)
    {
        $totalUsers = $department->users->count();
        $activeUsers = User::where('work_zone_id', $department->id)
            ->whereHas('tasks')
            ->count();

        $totalTasks = Task::whereHas('user', function($query) use ($department) {
            $query->where('work_zone_id', $department->id);
        })->count();

        $totalKpis = Kpi::whereHas('tasks.user', function($query) use ($department) {
            $query->where('work_zone_id', $department->id);
        })->count();

        $averageScore = KpiScore::whereHas('user', function ($query) use ($department) {
            $query->where('work_zone_id', $department->id);
        })
            ->whereNotNull('score')
            ->avg('score') ?? 0;

        // Calculate department ranking
        $departmentRank = WorkZone::selectRaw('work_zones.*, AVG(kpi_scores.score) as avg_score')
                ->leftJoin('users', 'work_zones.id', '=', 'users.work_zone_id')
                ->leftJoin('tasks', 'users.id', '=', 'tasks.user_id')
                ->leftJoin('kpis', function($join) {
                    $join->on('tasks.kpi_id', '=', 'kpis.id');
                })
                ->leftJoin('kpi_scores', function($join) {
                    $join->on('kpi_scores.kpi_id', '=', 'kpis.id')
                        ->whereNotNull('kpi_scores.score');
                })
                ->groupBy('work_zones.id')
                ->orderByDesc('avg_score')
                ->pluck('work_zones.id')
                ->search($department->id) + 1;

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'total_tasks' => $totalTasks,
            'total_kpis' => $totalKpis,
            'average_score' => round($averageScore, 1),
            'department_rank' => $departmentRank
        ];
    }

    private function getDepartmentUsersPerformance($department)
    {
        return $department->users->filter(function ($user) {
            return in_array($user->role_id, [2, 3]);
        })->map(function ($user) {
            $tasksCount = $user->tasks->count();
            $reviewedTasks = $user->tasks->filter(function($task) {
                return $task->comments->count() > 0;
            })->count();


            $scoredKpis = Kpi::whereHas('kpi_scores', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereNotNull('score');

            })->count();


            $averageScore = KpiScore::whereHas('user', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                    ->whereNotNull('score')
                    ->avg('score') ?? 0;

            $user->tasks_count = $tasksCount;
            $user->reviewed_tasks = $reviewedTasks;
            $user->scored_kpis = $scoredKpis;
            $user->average_score = round($averageScore, 1);

            return $user;
        });
    }

    private function getDepartmentKpis($department)
    {
        return Kpi::with(['children' => function($query) use ($department) {
            $query->with(['tasks' => function($taskQuery) use ($department) {
                $taskQuery->whereHas('user', function($userQuery) use ($department) {
                    $userQuery->where('work_zone_id', $department->id);
                });
            }]);
        }])
            ->whereNull('parent_id')
            ->get()
            ->map(function($category) use ($department) {
                $totalScore = 0;
                $scoredChildren = 0;

                $category->children = $category->children->map(function($child) use ($department, &$totalScore, &$scoredChildren) {
                    $child->department_tasks_count = $child->tasks->filter(function($task) use ($department) {
                        return $task->user->department_id == $department->id;
                    })->count();

                    if ($child->score) {
                        $totalScore += $child->score->score;
                        $scoredChildren++;
                    }

                    return $child;
                });

                $category->average_score = $scoredChildren > 0 ? $totalScore / $scoredChildren : null;

                return $category;
            });
    }

    private function enrichDepartmentData($department)
    {
        $activeUsers = User::where('department_id', $department->id)
            ->whereHas('tasks')
            ->count();

        $completedTasks = Task::whereHas('user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })->whereHas('comments')->count();

        $scoredKpis = Kpi::whereHas('tasks.user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })->whereNotNull('score')->count();

        $averageScore = Kpi::whereHas('tasks.user', function($query) use ($department) {
            $query->where('department_id', $department->id);
        })->whereNotNull('score')->avg('score') ?? 0;

        $department->active_users = $activeUsers;
        $department->completed_tasks = $completedTasks;
        $department->scored_kpis = $scoredKpis;
        $department->average_score = round($averageScore, 1);

        return $department;
    }

    private function calculateCompanyStats()
    {
        $totalDepartments = WorkZone::count();
        $totalUsers = User::count();
        $totalTasks = Task::count();
        $companyAverage = Kpi::whereNotNull('score')->avg('score') ?? 0;

        return [
            'total_departments' => $totalDepartments,
            'total_users' => $totalUsers,
            'total_tasks' => $totalTasks,
            'company_average' => round($companyAverage, 1)
        ];
    }

    private function calculateUserDetailedStats($user)
    {
        $totalTasks = $user->tasks->count();
        $reviewedTasks = $user->tasks->filter(function($task) {
            return $task->comments->count() > 0;
        })->count();

        $scoredKpis = Kpi::whereHas('tasks', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereNotNull('score')->count();

        $averageScore = Kpi::whereHas('tasks', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereNotNull('score')->avg('score') ?? 0;

        return [
            'total_tasks' => $totalTasks,
            'reviewed_tasks' => $reviewedTasks,
            'scored_kpis' => $scoredKpis,
            'average_score' => round($averageScore, 1),
            'completion_rate' => $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0,
            'review_rate' => $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0
        ];
    }

    private function getDepartmentComparison($department)
    {
        // Get all departments for comparison
        $allDepartments = WorkZone::with(['users', 'tasks'])
            ->withCount(['users', 'tasks'])
            ->get()
            ->map(function ($dept) {
                return $this->enrichDepartmentData($dept);
            })
            ->sortByDesc('average_score');

        // Calculate percentile ranking
        $totalDepartments = $allDepartments->count();
        $currentRank = $allDepartments->pluck('id')->search($department->id) + 1;
        $percentile = (($totalDepartments - $currentRank) / $totalDepartments) * 100;

        // Get top performers for comparison
        $topPerformers = $allDepartments->take(3);
        $bottomPerformers = $allDepartments->reverse()->take(3);

        return [
            'all_departments' => $allDepartments,
            'current_rank' => $currentRank,
            'total_departments' => $totalDepartments,
            'percentile' => round($percentile, 1),
            'top_performers' => $topPerformers,
            'bottom_performers' => $bottomPerformers
        ];
    }


}
