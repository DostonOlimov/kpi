<?php


namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\KpiEmployees;
use App\Models\Month;
use App\Models\Razdel;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Rules\MonthYearExists;


class DirectorProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // only razdel equal 1 from kpi_director
        $kpi = Director::where('razdel', '=', 1)
            ->where('status', '=', 'active')
            ->where('work_zone_id','=',$user->work_zone_id);

        $data1 = $kpi->get();
        $SumWorksCount = $kpi->sum('works_count');
        //get kpi_employess has kpi_dir_id
        $kpi_emp = KpiEmployees::has('kpi_dir')
            ->where('work_zone_id','=',$user->work_zone_id);
        $TotalWorksCount = $kpi_emp->sum('works_count');
        $TotalCurrentWorks = $kpi_emp->sum('current_works');

        $month = Month::getMonth(session('month') ?? date('m'));
        $razdel = Razdel::all();

        return view('director.list', compact('data1','SumWorksCount','TotalWorksCount','TotalCurrentWorks','razdel','month'));
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
        $month_name = Month::getMonth(session('month') ?? date('m'));
        return view('director.add', [
            'data' => $data,
            'month_id' => session('month') ?? date('m'),
            'year' => session('year') ?? date('y'),
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
    public function employees(Request $request)
    {
        $user = auth()->user();
        $users = User::with('totalBalls')
        ->where('work_zone_id','=',$user->work_zone_id)
        ->where('role_id','=',3)
        ->get();

        $chart_data = array();
        foreach($users as $user){
            $chart_data[] = ['name'=>$user->first_name.' '.$user->last_name,'ball'=>optional($user->totalBalls->first())->current_ball];
        }
        return view('director.employees', compact('users','chart_data'));
    }

    public function warn()
    {
        return view('director.warn');
    }




}
