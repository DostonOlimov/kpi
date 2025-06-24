<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TotalBall extends Model
{
    protected $table = 'users_total_balls';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'fine_ball',
        'personal_ball',
        'current_ball',
        'max_ball',
        'month',
        'year',
        'created_at',
        'updated_at'
    ];

    public function CalculateRating(){
        $rating = 0;
        if ($this->current_ball < 60) {
            $rating = 0;
        } elseif ($this->current_ball < 70) {
            $rating = 2;
        } elseif ($this->current_ball < 85) {
            $rating = 2;
        } else {
            $rating = 2;
        }
        return $rating;
    }

    public function Calculate(int $user_id, int $month,int $year)
    {
        $data = KpiEmployees::where('user_id','=',$user_id)
            ->where('month', '=' ,$month)
            ->where('year','=',$year)
            ->where('status','=','active')
            ->get();
        $personal_ball = 0;
        $fine_ball = 0;
        foreach ($data as $key => $item){
            $personal_ball += KpiEmployees::find($item->id)->CalculateBall();
            if($t = FineBall::where('kpi_id','=',$item->id)->first())
                $fine_ball += $t->fine_ball;
        }
        $ball = $this::where('user_id','=',$user_id)
            ->where('month','=',$month)
            ->where('year','=',$year)
            ->first();
        if ( !is_null($ball)){
            $ball->personal_ball = $personal_ball;
            $ball->fine_ball =  $fine_ball;
            $ball->current_ball = $personal_ball - $fine_ball;
            $ball->current_ball = $personal_ball - $fine_ball;
            $ball->save();
        }
        else{
            $this->user_id = $user_id;
            $this->personal_ball = $personal_ball;
            $this->fine_ball = 0;
            $this->current_ball = $personal_ball;
            $this->max_ball = 90;
            $this->year = $year;
            $this->month = $month;
            $this->save();
        }
        return true;
    }

    public function getEmployeesBalls(int $user_id,int $year)
    {
    $data1 = $this::select('month','current_ball')
        ->where('user_id','=',$user_id)
        ->where('year','=',$year)
        ->pluck('current_ball','month');
    $balls = [];
    foreach($data1 as $key => $ball)
    {
       $balls[] = ['year'=>Month::getMonth($key),'income'=>$ball];
    }
    $balls[] = ['year'=>"Max",'income'=>100];
    return $balls;
    }
}

