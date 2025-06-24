<?php


namespace App\Http\Controllers;

use App\Models\FineBall;
use App\Models\EmployeeSumma;
use App\Models\KpiEmployees;
use App\Models\TotalBall;
use App\Models\WorkZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Month;
use Response;

class KomissionController extends Controller
{

    public function index(Request $request)
    {
        $month = Month::requestMonth($request);
        $year = Month::requestYear($request);
        $users = User::with(['totalBalls' => function($query) use ($month,$year) {
            $query->where('month', '=', $month)
                ->where('year','=',$year);
        }])
        ->with('work_zone')
        ->where('role_id','=',3)
        ->get();
        $date = ['month'=>$month,'year'=>$year,'month_name'=>Month::getMonth($month)];
        return view('commission.list',compact('users','date'));

    }

    public function section(Request $request)
    {
        $month = Month::requestMonth($request);
        $year = Month::requestYear($request);
        $sections = WorkZone::all();
        $chart_data = array();
        foreach($sections as $section){
            $section->section_ball = $section->getSectionBallAverage($month,$year);
            $chart_data[] = ['year'=>$section->name,'income'=>$section->section_ball];
        }

        return view('commission.section', [
            'section'=> $sections,
            'data1' => $chart_data,
            'month_name' => Month::getMonth($month),

        ]);
    }

    public function calculate( $id , $month_id,$year)
    {

        $ball = new TotalBall();
        $ball->Calculate($id,$month_id,$year);
        return back()
            ->with('success','Malumotlar muvaffaqiyatli yuklandi.');
    }


    public function edit( $user_id,$month_id,$year)
    {

        $data = KpiEmployees::where('user_id','=',$user_id)
                ->where('month','=',$month_id)
                ->where('year','=',$year)
                ->orderBy('razdel', 'ASC')
                ->get();
        $user = User::find($user_id);
        $ball = new TotalBall();
        $balls = $ball->getEmployeesBalls($user_id,$year);
        return view('commission.edit', compact('data','user','balls'));

    }

    public function add(Request $request, $id)
    {
        $user = auth()->user();
        $data = DB::table('')
            ->where('work_type', '=', $user['work_type'])
            ->get();

        return view('commission.add', [
            'data' => $data,

        ]);
    }
    public function upload(Request $request)
    {
        $works = KpiEmployees::find($request->id)->CalculateBall();
        $request->validate([
            'fine_ball' => ['required','numeric', 'min:0',"max:{$works}", 'regex:/^\d+(\.\d{1,2})?$/'],
            'file' => ['required','mimes:docx,doc,pdf,png,jpg','max:4096'],
            'commit' => ['required','string',]
        ],
        [
            'fine_ball.required' => 'Chegirma bali to\'ldirilish shart.',
            'fine_ball.max' => 'Chegirma bali to\'plangan baldan oshmasligi kerak.',
            'fine_ball.min' => 'Chegirma bali noldan katta bo\'lishi kerak.',
            'file.required' => 'Fayl to\'ldirilish shart.',
            'file.mimes' => 'Fayl kengaytmasi docx, doc, pdf, png, jpg bo\'lishi kerak.',
            'file.max' => 'Fayl o\'lchami maksimal 4 mb .',
            'commit.required' => 'Izoh to\'ldirilish shart.',
        ]);
        if($request->file()) {
            $fileName = time().'_'.$request->file->getClientOriginalName();
            $filePath = public_path('/storage/uploads/');
            $request->file('file')->move($filePath, $fileName);
            $kpi = KpiEmployees::find($request->id);
            $fine = new FineBall();
            $fine->user_id = $kpi->user_id;
            $fine->kpi_id = $kpi->id;
            $fine->fine_ball = $request->fine_ball;
            $fine->order_file =  $filePath.$fileName;
            $fine->comment = $request->commit;
            $fine->month_num = $kpi->month;
            $fine->year = $kpi->year;
            $fine->save();
            $total_ball = TotalBall::where('user_id', '=', $kpi->user_id)
                ->where('month' ,'=',$kpi->month)
                ->where('year' ,'=',$kpi->year)
                ->first();
            $total_ball->fine_ball = $total_ball->fine_ball + $request->fine_ball;
            $total_ball->current_ball = $total_ball->personal_ball - $total_ball->fine_ball - $request->fine_ball;
            $total_ball->save();
            $kpi->fine_ball = $request->fine_ball;
            $kpi->save();
            $ball = new TotalBall();
            $ball->Calculate($kpi->user_id,$kpi->month,$kpi->year);

            return back()
                ->with('success','Malumotlar muvaffaqiyatli yuklandi.');
        }
    }
    public function download($id)
    {
        $path = FineBall::find($id)->order_file;

        return Response::download($path);
    }
    public function store(Request $request)
    {
        if ($request->file()) {
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('uploads', $fileName, 'public');
            $kpi =  KpiEmployees::find($request->id);
            $kpi->current_works = $request->curent_works;
            $kpi->file_name = time() . '_' . $request->file->getClientOriginalName();
            $kpi->file_path = '/storage/' . $filePath;
            $kpi->save();
            return back()
                ->with('success', 'Malumotlar muvaffaqiyatli yuklandi.')
                ->with('file', $fileName);
        }

    }
      public function AddBall(Request $request)
       {
           $request->validate([
               'current_ball' => ['required', 'numeric', 'min:0', 'max:100.00']
           ]);
           if ($request->current_ball) {
            $current_ball = $request->current_ball;
            $user_id = $request->user_id;
            $month = $request->month;
            $year = $request->year;

               DB::table('users_total_balls')
                   ->insert([
                       'user_id' => $user_id,
                       'personal_ball' => 0,
                       'fine_ball' => 0,
                       'current_ball' => $current_ball,
                       'max_ball' => 90,
                       'year' => $year,
                       'month' => $month
                   ]);
            $sum = new EmployeeSumma();
            $sum->CalculateSumma($user_id,$month,$year);

               return back()
                   ->with('success', 'Malumotlar muvaffaqiyatli yuklandi.');
           }

   }
   public function BallEdit( $id)
   {
      $user = DB::table('users_total_balls')->where('id','=',$id)->first();

       return view('commission.balledit', ['user' => $user

       ]);
   }
   public function store2(Request $request)
   {
       $request->validate([
           'current_ball' => ['required','numeric', 'min:0','max:100.00']
       ]);

       $current_ball = $request->current_ball;
       $ball = TotalBall::find($request->id);
       $user_id =  $ball->user_id;
       $ball->current_ball = $current_ball;
       $ball->save();
       $month = $ball->month;
       $year = $ball->year;
       $sum = new EmployeeSumma();
       $sum->CalculateSumma($user_id,$month,$year);


       return redirect(route('commission.list'));

   }

}
