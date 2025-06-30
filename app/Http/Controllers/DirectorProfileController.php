<?php


namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\Kpi;
use App\Models\KpiEmployees;
use App\Models\KpiScore;
use App\Models\Month;
use App\Models\Razdel;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkZone;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Rules\MonthYearExists;


class DirectorProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $users = User::with('tasks' )
            ->where('work_zone_id','=',$user->work_zone_id)
            ->where('role_id','=',User::ROLE_USER)
            ->get();
       return view('director.employees', ["users" => $users]);

    }

    public function check_user(User $employee, Request $request)
    {
        $kpis = Kpi::whereNull('parent_id')
            ->where('type',Kpi::TYPE_1)
            ->with(['children.tasks' => function ($query) use ($employee) {
                $query->where('user_id', $employee->id);
            }])
            ->get();

        $total = 0;
        $checked = 0;

        foreach ($kpis as $kpi) {
            foreach ($kpi->children as $child) {
                $tasks = $child->tasks;
                $total += $tasks->count();
                $checked += $tasks->where('is_checked', true)->count();
            }
        }

        return view('director.checking', [
            'kpis' => $kpis,
            'month' => session('month') ?? date('m'),
            'year' => session('year') ?? date('Y'),
            'total' => $total,
            'checked' => $checked,
            'user' => $employee,
        ]);
    }



    /**
     * @return Application|Factory|View
     */
    public function add(Request $request)
    {
        $user = auth()->user();

        $data = Director::where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'inactive')
            ->get();
        $month_name = Month::getMonth(session('month') ?? (int)date('m'));
        return view('director.add', [
            'data' => $data,
            'month_id' => session('month') ?? (int)date('m'),
            'year' => session('year') ?? (int)date('y'),
            'month_name' => $month_name
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $name = (string)$request->input('name');
        $weight = (float)$request->input('weight');
        $works = (int)$request->input('works');
        $month = (int)$request->input('month_id');
        $year = (int)$request->input('year');

        $data = Director::where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'inactive')
            ->where('month', '=',$month)
            ->where('year','=',$year)
            ->get();

        $kpi_dir = new Director();
        $kpi_dir->name = $name;
        $kpi_dir->user_id = $user->id;
        $kpi_dir->work_zone_id = $user->work_zone_id;
        $kpi_dir->razdel = 1;
        $kpi_dir->weight = $weight;
        $kpi_dir->current_ball = 0;
        $kpi_dir->works_count = $works;
        $kpi_dir->month = $month;
        $kpi_dir->status = 'inactive';
        $kpi_dir-> band_id = count($data) + 1;
        $kpi_dir->year = $year;
        $kpi_dir->save();
        return 'ok';
    }

    public function delete($id)
    {
        $data = DB::table('kpi_director');
        $month_id = $data->find($id)->month;
        $year = $data->find($id)->year;
        $data->delete($id);
        return redirect(route('director.add',[$month_id,$year]));
    }

    public function commit(Request $request)
    {
        $user = auth()->user();

        $kpi_dir = new Director();
        $kpi_dir->where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('month','=',$request->month_id)
            ->where('year','=',$request->year)
            ->update([
                'status' => 'active'
            ]);
        return 'ok';
    }
 /**
     * @return \Illuminate\Http\Response
     */

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
                        $totalScore += $child->score;
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
