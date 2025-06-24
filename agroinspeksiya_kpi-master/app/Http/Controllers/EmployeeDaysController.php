<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSumma;
use App\Models\KpiEmployees;
use App\Models\TotalBall;
use Illuminate\Http\Request;
use App\Models\EmployeeDays;
use App\Models\Month;
use App\Models\User;
use App\Rules\ExistMonthforEmployee;
use Illuminate\Support\Facades\DB;

class EmployeeDaysController extends Controller
{
      /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index($month_id)
    {
        $roles = EmployeeDays::orderBy('id','desc')->paginate(10);
        return view('days.index', compact('roles'));
    }
       /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function list($month_id,$year)
    {
        $users = User::with(['employeeDays' => function($query) use ($month_id,$year) {
            $query->where('month_id', '=', $month_id)
                ->where('year','=',$year);
        }])
            ->where('role_id','=',3)
            ->get();
        $days = Month::where('month_id','=',$month_id)
            ->where('year','=',$year)
            ->first()
            ->days;
        $date['month_name'] = Month::getMonth($month_id);
        $date['month_id'] = $month_id;
        $date['year'] = $year;
        $date['days'] = $days;
        return view('days.list', compact('users','date'));
    }
       /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function select()
    {
        $roles = EmployeeDays::orderBy('id','desc')->paginate(10);
        return view('days.select', compact('roles'));
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function createday($id,$month_id,$year)
    {
        return view('days.createday',['id' => $id,'month_id' => $month_id,'year' => $year]);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $day =Month::where('month_id','=',$request->month_id)
            ->where('year','=',$request->year)
            ->value('days');

        $request->validate([
            'user_id' => 'required',
            'month_id' => 'required',
            'days' => ['required','numeric', 'min:0',"max:{$day}", 'regex:/^\d+(\.\d{1,2})?$/'],
        ],
        [
            'days.max' => "Xodimning ish kunlari dan katta bo'laolmaydi."

        ]);
        EmployeeDays::create($request->post());
        $user_id = $request->user_id;
        $month = $request->month_id;
        $year = $request->year;
        $kpi_req = DB::table('kpi_required')
            ->where('razdel_id','=',3)
            ->get();
        $user = User::find($user_id);
        foreach($kpi_req as $key=>$value){
            $kpi = new KpiEmployees;
            $kpi->name = $value->name;
            $kpi->user_id = $user_id;
            $kpi->work_zone_id = $user->work_zone_id;
            $kpi->razdel = $value->razdel_id;
            $kpi->weight = $value->weight;
            $kpi->current_ball = 0;
            $kpi->works_count = $day;
            $kpi->current_works = $request->days;
            $kpi->month = $month;
            $kpi->year = $year;
            $kpi->status = 'active';
            $kpi->band_id = $key+1;
            $kpi->kpi_dir_id = 0;
            $kpi->save();
        }
            $ball = new TotalBall();
            $ball->Calculate($user_id,$month,$year);
            $sum = new EmployeeSumma();
            $sum->CalculateSumma($user_id,$month,$year);
            
        return redirect()->route('days.list',[$month,$year])->with('success','Ish kuni muvaffaqatli yaratildi.');
    }
     /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store2(Request $request)
    {

        $request->validate([
            'month_id' => ['required', new ExistMonthforEmployee('months', $request->year ,$request->month_id)],
        ],);

        return redirect()->route('days.list',[$request->month_id,$request->year]);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\company  $company
    * @return \Illuminate\Http\Response
    */
    public function show()
    {
        $roles = EmployeeDays::orderBy('id','desc')->paginate(10);
        $months = Month::getMonth();
        return view('days.show', compact('roles','months'));

    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Company  $company
    * @return \Illuminate\Http\Response
    */
    public function edit($day)
    {
        $days = EmployeeDays::find($day);
        return view('days.edit',compact('days'));
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\company  $company
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request,$day)
    {
        $days = EmployeeDays::find($day);
        $month = $days->month_id;
        $user_id = $days->user_id;
        $year = $days->year;
        $d =Month::where('month_id','=',$month)
            ->where('year','=',$year)
            ->value('days');

        $request->validate([
            'days' => ['required','numeric', 'min:0',"max:{$d}", 'regex:/^\d+(\.\d{1,2})?$/'],
        ]);
        $days->days = $request->days;
        $days->save(); 

        $kpi_req = DB::table('kpi_required')
            ->where('razdel_id','=',3)
            ->get();
        $user = User::find($user_id);
        foreach($kpi_req as $key=>$value){
            $kpi = KpiEmployees::where('user_id', '=', $user->id)
                ->where('status','=','active')
                ->where('razdel','=',3)
                ->where('month','=',$month)
                ->where('year','=',$year)
                ->first();
            $kpi->current_works = $request->days;
            $kpi->save();
        }
        $ball = new TotalBall();
        $ball->Calculate($user_id,$month,$year);
        $sum = new EmployeeSumma();
        $sum->CalculateSumma($user_id,$month,$year);
       
        return redirect()->route('days.list',[$month,$year])->with('success','Ma\'lumotlar muvaffaqiyatli o\'zgartirildi');
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Company  $company
    * @return \Illuminate\Http\Response
    */
    public function destroy(EmployeeDays $role)
    {
        $role->delete();
        return redirect()->route('days.index')->with('success','Ma\'lumotlar o\'chirildi');
    }
}
