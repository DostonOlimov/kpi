<?php


namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\KpiEmployees;
use App\Models\Month;
use App\Models\Razdel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Rules\MonthYearExists;
 

class DirectorProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $month_id = Month::requestMonth($request);
        $year = Month::requestYear($request);
        // only razdel equal 1 from kpi_director
        $kpi = Director::where('razdel', '=', 1)
            ->where('status', '=', 'active')
            ->where('work_zone_id','=',$user->work_zone_id)
            ->where('month','=', $month_id)
            ->where('year','=',$year);
        $data1 = $kpi->get();
        $SumWorksCount = $kpi->sum('works_count');
        //get kpi_employess has kpi_dir_id
        $kpi_emp = KpiEmployees::has('kpi_dir')
            ->where('work_zone_id','=',$user->work_zone_id)
            ->where('month','=', $month_id)
            ->where('year','=',$year);
        $TotalWorksCount = $kpi_emp->sum('works_count');
        $TotalCurrentWorks = $kpi_emp->sum('current_works');

        $month = Month::getMonth($month_id);
        $razdel = Razdel::all();
       
        return view('director.list', compact('data1','SumWorksCount','TotalWorksCount','TotalCurrentWorks','razdel','month'));
    }

        /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function add2( Request $request)
    {
        return view('director.select');
    }
    /**
     * @return \Illuminate\Http\Response
     */
    public function month_store(Request $request)
    {
        $user = auth()->user();
        $data = new Director();
        $arr = $data->getTextMonthsName($user->id,$request->year);

        $request->validate([
            'month_id' => ['required', new MonthYearExists('months', $request->month_id .'-'. $request->year),
                            Rule::NotIn($arr)],
        ],
        [
            'month_id.not_in' => 'Ushbu oy uchun ma\'lumotlar to\'liq kiritilgan.'

        ]);
        return redirect()->route('director.add',['month'=>$request->month_id,'year'=>$request->year]);
    }
    /**
     * @param int month_id
     * @param int year
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request,$month_id,$year)
    {
        $user = auth()->user();

        $data = Director::where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'inactive')
            ->where('month', '=',$month_id)
            ->where('year','=',$year)
            ->get();
        $month_name = Month::getMonth($month_id);
        return view('director.add', [
            'data' => $data,
            'month_id' => $month_id,
            'year' => $year,
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
        $month = Month::requestMonth($request);
        $year = Month::requestYear($request);
        $users = User::with(['totalBalls' => function($query) use ($month,$year) {
            $query->where('month', '=', $month)
                ->where('year','=',$year);
        }])
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
