<?php


namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\Kpi;
use App\Models\KpiEmployees;
use App\Models\Month;
use App\Models\Task;
use App\Models\TotalBall;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Rules\DirectorKpiExist;

class EmployeeProfileController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get user's KPIs with tasks and scores
        $kpis = KPI::with([
            'children' => function($query) use ($userId) {
                $query->with([
                    'tasks' => function($taskQuery) use ($userId) {
                        $taskQuery->where('user_id', $userId)
                            ->with(['comments.user']);
                    }
                ]);
            }
        ])
            ->whereNull('parent_id')
            ->get();

        // Process KPIs to add user-specific data
        $kpis = $kpis->map(function($category) use ($userId) {
            $category->user_tasks_count = 0;
            $totalScore = 0;
            $scoredChildren = 0;

            $category->children = $category->children->map(function($child) use ($userId, &$totalScore, &$scoredChildren) {
                // Get user's tasks for this child
                $child->user_tasks = $child->tasks->where('user_id', $userId);

                // Count user tasks for this category
                $userTasksCount = $child->user_tasks->count();

                if ($child->score) {
                    $totalScore += $child->score;
                    $scoredChildren++;
                }

                return $child;
            });

            // Calculate average score for category
            $category->average_score = $scoredChildren > 0 ? $totalScore / $scoredChildren : null;

            return $category;
        });

        // Calculate user statistics
        $userStats = $this->calculateUserStats($userId);

        // Get achievements
        $achievements = $this->getUserAchievements($userId, $userStats);

        return view('kpi_forms.list', compact('kpis', 'userStats', 'achievements'));
    }

    private function calculateUserStats($userId)
    {
        $totalTasks = Task::where('user_id', $userId)->count();
        $reviewedTasks = Task::where('user_id', $userId)
            ->whereHas('comments')
            ->count();

        $scoredKPIs = KPI::whereHas('tasks', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
//            ->whereNotNull('kpi_scores')
            ->count();

        $averageScore =0;

        $completionRate = $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0;
        $reviewRate = $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0;

        $totalKPIs = KPI::whereHas('tasks', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();

        $scoringProgress = $totalKPIs > 0 ? ($scoredKPIs / $totalKPIs) * 100 : 0;


        return [
            'total_tasks' => $totalTasks,
            'reviewed_tasks' => $reviewedTasks,
            'scored_kpis' => $scoredKPIs,
            'average_score' => round($averageScore, 1),
            'completion_rate' => round($completionRate, 1),
            'review_rate' => round($reviewRate, 1),
            'scoring_progress' => round($scoringProgress, 1)
        ];
    }

    private function getUserAchievements($userId, $userStats)
    {
        $achievements = [];

        // First Task Achievement
        if ($userStats['total_tasks'] >= 1) {
            $achievements[] = [
                'icon' => '🎯',
                'title' => 'First Step',
                'description' => 'Submitted your first task'
            ];
        }

        // Task Master Achievement
        if ($userStats['total_tasks'] >= 10) {
            $achievements[] = [
                'icon' => '📝',
                'title' => 'Task Master',
                'description' => 'Submitted 10+ tasks'
            ];
        }

        // High Performer Achievement
        if ($userStats['average_score'] >= 80) {
            $achievements[] = [
                'icon' => '⭐',
                'title' => 'High Performer',
                'description' => 'Maintained 80+ average score'
            ];
        }

        // Perfect Score Achievement
        if ($userStats['average_score'] >= 95) {
            $achievements[] = [
                'icon' => '🏆',
                'title' => 'Excellence',
                'description' => 'Achieved 95+ average score'
            ];
        }

        // Consistent Performer Achievement
        if ($userStats['completion_rate'] >= 90) {
            $achievements[] = [
                'icon' => '🎖️',
                'title' => 'Consistent Performer',
                'description' => '90%+ task completion rate'
            ];
        }

        // Feedback Champion Achievement
        if ($userStats['review_rate'] >= 80) {
            $achievements[] = [
                'icon' => '💬',
                'title' => 'Feedback Champion',
                'description' => '80%+ of tasks reviewed'
            ];
        }

        return $achievements;
    }

    public function create(Request $request)
    {
        $kpis = Kpi::whereNull('parent_id')
        ->where('type',Kpi::TYPE_1)
        ->with(['children.tasks' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
        ->get();

        return view('kpi_forms.create', [
            'kpis' => $kpis,
            'month' => session('month') ?? date('m'),
            'year' => session('year') ?? date('Y'),
        ]);
    }
    public function save(Request $request)
    {

        $user = auth()->user();
        try {
            $name = (string)$request->input('name');
            $weight = (int)$request->input('weight');
            $month_id = (int)$request->input('month_id');
            $year = (int)$request->input('year');
            $arr = explode('&', $request->input('band'));
            $data = Director::find((int)$arr[1] );
            $band = (int)$arr[0];
            $works = (int)$request->input('works');

            $kpi_dir = new KpiEmployees();
            $kpi_dir->name = $name;
            $kpi_dir->user_id = $user->id;
            $kpi_dir->kpi_dir_id = (int)$arr[1];
            $kpi_dir->work_zone_id = $user->work_zone_id;
            $kpi_dir->razdel = 1;
            $kpi_dir->weight = $weight;
            $kpi_dir->current_ball = 0;
            $kpi_dir->works_count = $works;
            $kpi_dir->month = $month_id;
            $kpi_dir->status = 'inactive';
            $kpi_dir->band_id = $band;
            $kpi_dir->year = $year;
            $kpi_dir->save();

            Director::where('id','=',(int)$arr[1] )
                ->where('razdel', '=', 1)
                ->where('month','=',$month_id)
                ->update(['taken_works' => $data->taken_works + $works]);

            return back();
        } catch (\Exception $exception) {
            return back();
        }
    }

    public function delete($id)
    {
        $data = KpiEmployees::find($id);
        $kpi = Director::find($data->kpi_dir_id);
        if($kpi){
            $kpi->taken_works = $kpi->taken_works - $data->works_count;
            $kpi->save();
        }
        $data->delete($id);
        return redirect(route('profile.create',[$data->month,$data->year]));
    }

    public function commit(Request $request)
    {
        $user = auth()->user();
        $month = (int)$request->input('month_id');
        $year = (int)$request->input('year');
        $kpi_req = DB::table('kpi_razdel')
            ->where('id','=',4)
            ->first();
            $kpi_dir = new KpiEmployees();
            $kpi_dir->name = $kpi_req->name;
            $kpi_dir->user_id = $user->id;
            $kpi_dir->kpi_dir_id = 0;
            $kpi_dir->work_zone_id = $user->work_zone_id;
            $kpi_dir->razdel = $kpi_req->id;
            $kpi_dir->weight = 10;
            $kpi_dir->current_ball = 10;
            $kpi_dir->current_works = 10;
            $kpi_dir->works_count = 10;
            $kpi_dir->month = $month;
            $kpi_dir->status = 'active';
            $kpi_dir->band_id = 0;
            $kpi_dir->year = $year;
            $kpi_dir->save();

        KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('month','=',$month)
            ->where('year','=',$year)
            ->update([
                'status' => 'active'
            ]);
        $ball = new TotalBall();
        $ball->Calculate($user->id,$month,$year);
        return 'ok';
    }

    public function save2(Request $request)
    {
        $user = auth()->user();
        $works_count = (int)$request->input('name');
        $month = (int)$request->input('month');
        $year = (int)$request->input('year');

        $kpi_req = DB::table('kpi_required')
            ->where('id','=',$request->input('id'))
            ->first();
        $kpi_dir = new KpiEmployees();
            $kpi_dir->name = $kpi_req->name;
            $kpi_dir->user_id = $user->id;
            $kpi_dir->kpi_dir_id = 0;
            $kpi_dir->work_zone_id = $user->work_zone_id;
            $kpi_dir->razdel = $kpi_req->razdel_id;
            $kpi_dir->weight = $kpi_req->weight;
            $kpi_dir->current_ball = 0;
            $kpi_dir->current_works = 0;
            $kpi_dir->works_count =  $works_count;
            $kpi_dir->month = $month;
            $kpi_dir->status = 'active';
            $kpi_dir->band_id =  $kpi_req->id;
            $kpi_dir->year = $year;
            $kpi_dir->save();

        return redirect(route('profile.create',[$month,$year]));
    }

    public function upload(Request $request)
    {
        $user = auth()->user();

        $data1 = KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'active')
            ->get();
        $data2 = KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 2)
            ->get();

        $razdel = DB::table('kpi_razdel')
            ->get();

        return view('kpi_forms.upload', [
            'data1' => $data1,
            'data2' => $data2,
            'razdel' => $razdel,
        ]);
    }

    public function ImageStore(Request $request)
    {
    	 $request->validate([
            'file' => 'required|mimes:docx,doc,pdf,png,jpg|max:4096',
            'curent_works' => ['required','numeric', 'min:0']
        ]);
        if($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = public_path('/storage/uploads/');
            $request->file('file')->move($filePath, $fileName);
            DB::table('kpi_employee')
                    ->where('id', '=', $request->id)
                    ->update([
                        'current_works' => $request->curent_works,
                        'file_name' => time().'_'.$request->file->getClientOriginalName(),
                        'file_path' =>  $filePath.$fileName,
                    ]);
        $kpi = KpiEmployees::find($request->id);
        $ball = new TotalBall();
        $ball->Calculate($kpi->user_id,$kpi->month,$kpi->year);
            return back()
            ->with('success','Malumotlar muvaffaqiyatli yuklandi.')
            ->with('file', $fileName);
        }
    }

    public function download($id)
    {

        // $file = public_path().'/storage/uploads/1680112587_Task3.pdf';
        $path = DB::table('kpi_employee')
        ->find($id)
        ->file_path;
        return Response::download($path);
        // return  \Illuminate\Support\Facades\Storage::download( $file);
    }
}
