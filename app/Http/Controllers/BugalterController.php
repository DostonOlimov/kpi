<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDays;
use App\Models\EmployeeSumma;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Global_;

class BugalterController extends Controller
{

    public function index(Request $request)
    {
        $month_id = session('month') ?? (int)date('m');
        $data = EmployeeSumma::with('users')
            ->get();
            $work_day = Month::where('month_id','=',$month_id)->first()->days ?? 21;
            $salary = [];
            $ustama = [];
            $soliq = [];
            $jami = [];
            $active = [];
            $new_ustama = [];
            $new_soliq = [];
            $new_total = [];
            foreach ($data as $item){
                array_push($salary, $item->summa);
                array_push($ustama, $item->ustama);
                array_push($soliq, $item->ustama * 0.25);
                array_push($jami, $item->total_summa);
                array_push($active, $item->active_summa);
                array_push($new_ustama, $item->new_ustama);
                array_push($new_soliq, $item->new_ustama * 0.25);
                array_push($new_total, $item->new_total);
            }

            return view('bugalter.list', compact('data','work_day','month_id','salary','ustama','soliq','jami','active','new_soliq','new_ustama','new_total'));
    }

    public function add()
    {
        $month = [
            'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'
        ];
        return view('bugalter.add_summa', [
            'month' => $month
        ]);
    }
    public function calculate($id)
    {
        $summa = DB::table('employees_summa')->find($id);
        $work_day = DB::table('months')
            ->where('month_id', '=', $summa->month)
            ->value('days');
        $days1 = DB::table('employee_days')
        ->where('user_id', '=', $summa->user_id)
        ->where('month_id','=',$summa->month)
        ->value('days');
        $salary = DB::table('users')
        ->where('id', '=', $summa->user_id)
        ->value('salary');
        DB::table('employees_summa')
        ->where('user_id', '=', $summa->user_id)
        ->where('month','=',$summa->month)
        ->update([
                'ustama' => ($summa->current_ball * $summa->rating * $salary * $days1) / (100 * $work_day),
                'total_summa' => (($summa->current_ball * $summa->rating * $salary * $days1) / (100 * $work_day)) * 1.25,
                'active_summa' => 0,
        ]);
        return back()
        ->with('success','Malumotlar muvaffaqiyatli yuklandi.');
    }
    public function edit($id)
    {
        $data = DB::table('bugalter_summa')
            ->where('id', '=', $id)
            ->first();
        $month = [
            'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'
        ];
        return view('bugalter.edit', [
            'data' => $data,
            'month' => $month
        ]);
    }

    public function check()
    {
        $user = auth()->user();
        $data = DB::table('bugalter_summa')
            ->where('user_id', '=', $user->id)
            ->get();
        $month = [
            'Yanvar', 'Fevral', 'Mart', 'Aprel', 'May', 'Iyun', 'Iyul', 'Avgust', 'Sentyabr', 'Oktyabr', 'Noyabr', 'Dekabr'
        ];
        return view('bugalter.check', [
            'data' => $data,
            'month' => $month
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        DB::table('bugalter_summa')
            ->insert([
                'summa' => (double)$request->input('summa'),
                'month' => $request->input('month'),
                'user_id' => $user->id,
                'status' => 'inactive'
            ]);

        return redirect(route('bugalter.check'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        DB::table('bugalter_summa')
            ->where('id', '=', $id)
            ->update([
                'summa' => (double)$request->input('summa'),
                'month' => $request->input('month'),
                'user_id' => $user->id,
                'status' => 'inactive'
            ]);

        return redirect(route('bugalter.check'));
    }

    public function distribution(Request $request, $id)
    {

        $active_summa = DB::table('bugalter_summa')
            ->where('id', '=', $id)
            ->first();
        $total_summa = DB::table('employees_summa')
            ->where('month', '=', $active_summa->month)
            ->sum('total_summa');
        $sum_arr = DB::table('employees_summa')
            ->where('month', '=', $active_summa->month)
            ->get();
        $nisbat = $active_summa->summa / $total_summa;

        foreach ( $sum_arr as $arr )
        {
            $days = EmployeeDays::where('user_id' , '=',$arr->user_id)
                ->where('month_id' , '=' , $active_summa->month)
                ->value('days');
            $work_days = Month::where('month_id','=',$active_summa->month)
                ->value('days');
            $foiz = round($nisbat * round($arr->current_ball * $arr->rating)) ;

            DB::table('employees_summa')
                    ->where('id', '=', $arr->id)
                    ->update([
                        'foiz' => $foiz,
                        'new_ustama' => $arr->summa * $foiz * $days / $work_days / 100,
                        'new_total' => 1.25 * ($arr->summa * $foiz * $days / $work_days) /100,
                    ]);
        }
        if ($nisbat < 1) {
            foreach ($sum_arr as $arr){
                DB::table('employees_summa')
                    ->where('id', '=', $arr->id)
                    ->update([
                        'active_summa' => (int)($nisbat * $arr->total_summa)
                    ]);
            }
        } else {
            foreach ($sum_arr as $arr){
                DB::table('employees_summa')
                    ->where('id', '=', $arr->id)
                    ->update([
                        'active_summa' => $arr->total_summa
                    ]);
            }
        }
        DB::table('bugalter_summa')
            ->where('id', '=', $id)
            ->update([
                'status' => 'active'
            ]);
        return redirect(route('bugalter.list'));
    }

    public function get_summa(Request $request)
    {

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SummaExport, 'students.xlsx');
    }

}
