<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class EmployeeSumma extends Model
{
    protected $table = 'employees_summa';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'rating',
        'summa',
        'month',
        'status',
        'current_ball',
        'total_summa',
        'active_summa',
        'year',
        'ustama',
        'foiz',
        'new_ustama',
        'new_total'
    ];
    public function CalculateSumma(int $user_id, int $month,int $year)
    {
        $sum = $this::where('user_id','=',$user_id)
            ->where('month', '=' ,$month)
            ->where('year','=',$year)
            ->first();
        $ball = TotalBall::where('user_id','=',$user_id)
            ->where('month','=',$month)
            ->where('year','=',$year)
            ->first();
        $d =Month::where('month_id','=',$month)
            ->where('year','=',$year)
            ->value('days');
        $days = EmployeeDays::where('user_id','=',$user_id)
            ->where('month_id','=',$month)
            ->where('year','=',$year)
            ->value('days');
        $last_day = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();
        $salary = Salaries::whereRaw('? BETWEEN from_date AND to_date', [$last_day])
            ->orderBy('id', 'desc')
            ->first()
            ->value('salary');
        if ( $sum ){
            $this::find($sum->id);
            $this->rating = $ball->CalculateRating();
            $this->summa = $salary;
            $this->current_ball = $ball->current_ball;
            $this->ustama = ($ball->current_ball * $ball->rating * $salary * $days) / (100 * $d);
            $this->total_summa = (($ball->current_ball * $ball->rating * $salary * $days) / (100 * $d)) * 1.25;
            $this->active_summa = 0;
            $this->save();

        }
        else{
            $this->user_id = $user_id;
            $this->rating = $ball->CalculateRating();
            $this->summa = $salary;
            $this->month = $month;
            $this->year = $year;
            $this->current_ball = $ball->current_ball;
            $this->ustama = ($ball->current_ball * $ball->rating * $salary * $days) / (100 * $d);
            $this->total_summa = (($ball->current_ball * $ball->rating * $salary * $days) / (100 * $d)) * 1.25;
            $this->active_summa = 0;
            $this->save();
        }
        return true;
    }
    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
