<?php


namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\Kpi;
use App\Models\KpiEmployees;
use App\Models\Task;
use App\Models\TotalBall;
use App\Models\UserKpi;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeProfileController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get user's KPIs from user_kpis (UserKpi) with related kpi
        $userKpis = UserKpi::with(['kpi.parent', 'kpi.children','kpi.tasks'])
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

        $kpiTasks = Kpi::whereHas('tasks', function($taskQuery) use ($userId) {
            $taskQuery->where('user_id', $userId);
        })->count();

        $scoredKPIs = Kpi::whereHas('kpi_scores', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->whereIn('type', [1, 2, 3]);
        })->count();

        $kpis = Kpi::with(['kpi_scores' => function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->whereIn('type', [1, 2, 3]);
        }])->get();

        $averageScore = $kpis->map(function ($kpi) {
            $score = $kpi->kpi_scores->firstWhere('type', 3)
                ?? $kpi->kpi_scores->firstWhere('type', 2)
                ?? $kpi->kpi_scores->firstWhere('type', 1);

            return $score?->score ?? 0;
        })->sum();

        $completionRate = $totalTasks > 0 ? ($kpiTasks / $totalKPIs) * 100 : 0;
        $reviewRate = $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0;

        $scoringProgress = $totalKPIs > 0 ? ($scoredKPIs / $totalKPIs) * 100 : 0;


        return [
            'total_kpis' => $totalKPIs,
            'total_tasks' => $totalTasks,
            'reviewed_tasks' => $reviewedTasks,
            'scored_kpis' => $scoredKPIs,
            'average_score' => round($averageScore, 1),
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
            ->with(['kpi.parent', 'kpi.children.tasks'])
            ->get();

        // Extract the related KPIs
        $kpis = $userKpis->pluck('kpi')->filter();

        // Optionally, get unique parent KPIs
        $parentKpis = $kpis->pluck('parent')->filter()->unique('id')->values();

        return view('kpi_forms.create', [
            'kpis' => $kpis,
            'parent_kpis' => $parentKpis,
            'userId'=> $userId,
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
