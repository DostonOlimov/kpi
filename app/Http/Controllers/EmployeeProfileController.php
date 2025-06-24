<?php


namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\KpiEmployees;
use App\Models\Month;
use App\Models\TotalBall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \app\Models\User;
use App\Rules\DirectorKpiExist;
use Response;

class EmployeeProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $month_id = Month::requestMonth($request);
        $year = Month::requestYear($request);
        $kpi = new KpiEmployees();
        $data1 = $kpi->getData($user->id,1,$month_id,$year);
        $data2 = $kpi->getData($user->id,2,$month_id,$year);
        $data3 = $kpi->getData($user->id,3,$month_id,$year);
        $data4 = $kpi->getData($user->id,4,$month_id,$year);


        $data12 = TotalBall::select('month','current_ball')
            ->where('user_id','=',$user->id)
            ->where('year','=',$year)
            ->pluck('current_ball','month');
        // $balls = [];
        // $labels = array();

        // foreach($data12 as $key => $ball)
        // {
        //     $balls[Month::getMonth($key)] = $ball;
        // }
        // foreach (Month::getMonth() as $item)
        // {
        //     array_push($labels,$item);
        // }
        // array_push($labels,"Max ball");
        // $balls["Max ball"] = 100;
        $ball = new TotalBall();
        $balls = $ball->getEmployeesBalls($user->id,$year);
        return view('kpi_forms.list', [
            'data1' => $data1,
            'data2' => $data2,
            'data3' => $data3,
            'data4' => $data4,

            'balls' => $balls,
            'month_name' => Month::getMonth($month_id),
        ]);
    }

    public function add2( Request $request)
    {
        return view('kpi_forms.select');
    }

    public function month_store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'month_id' => ['required', new DirectorKpiExist('kpi_director', $user->work_zone_id,$request->month_id , $request->year)],
        ],);

        return redirect()->route('profile.create',[$request->month_id,$request->year]);
    }

    public function add(Request $request)
    {
        $user = auth()->user();
        $data = DB::table('kpi_employee')
            ->where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'active')
            ->get();
        return view('kpi_forms.addition', [
            'data' => $data
        ]);
    }

    public function create(Request $request,$month_id,$year)
    {
        $user = auth()->user();
        $data = Director::where('work_zone_id', '=', $user->work_zone_id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'active')
            ->where('month', '=', $month_id)
            ->where('year','=',$year)
            ->get();
        $data1 = KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'inactive')
            ->where('month', '=', $month_id)
            ->where('year','=',$year)
            ->get();
        $data2 = KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'active')
            ->where('month','=',$month_id)
            ->where('year','=',$year)
            ->first();
        $data3 = KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 2)
            ->where('status', '=', 'active')
            ->where('band_id','=',1)
            ->where('month','=',$month_id)
            ->where('year','=',$year)
            ->first();
        $data4 = KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 2)
            ->where('status', '=', 'active')
            ->where('band_id','=',2)
            ->where('month','=',$month_id)
            ->where('year','=',$year)
            ->first();
        $kpi_req = DB::table('kpi_required')
            ->where('razdel_id','=',2)
            ->get();
        if($data2){
            if($data3 && $data4){
                return view('kpi_forms.warn');
            }
            return view('kpi_forms.create2', [
                'data3' => $data3,
                'data4' => $data4,
                'month' => $month_id,
                'kpi_req' => $kpi_req,
                'year' => $year
            ]);
        }
        return view('kpi_forms.create', [
            'data' => $data,
            'data1' => $data1,
            'month' => $month_id,
            'year' => $year
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

        $month = Month::requestMonth($request);
        $year = Month::requestYear($request);
        $data1 = KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 1)
            ->where('month','=',$month)
            ->where('year','=',$year)
            ->where('status', '=', 'active')
            ->get();
        $data2 = KpiEmployees::where('user_id', '=', $user->id)
            ->where('razdel', '=', 2)
            ->where('month','=',$month)
            ->where('year','=',$year)
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
