<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkZone extends Model
{
    public $section_ball;
    use HasFactory;
    protected $fillable = [
        'name'
    ];
    public function users()
    {
        return $this->hasMany(User::class,);
    }
    public function getCountEmployeesAttribute()
    {
        return $this->users()->where('users.role_id','=',3)->count('id');
    }
    public function kpi_employee()
    {
        return $this->hasMany(KpiEmployees::class,);
    }
    public function getSectionBall($month,$year)
    {
        $data = $this->kpi_employee()
            ->where('month','=',$month)
            ->where('year','=',$year)
            ->whereIn('razdel',[1,2])
            ->get();
        $ball = 0;
        foreach ($data as $key => $item)
        {
            $ball += $item->CalculateBall()-$item->fine_ball;
        }
        return $this->count_employees != 0 ?  round(1.25*($ball / $this->count_employees),2) : 0;
    }
    public function getSectionBallAverage($month,$year)
    {
        $ball = 0;
        foreach($this->users()->get() as $user){
            $data = TotalBall::where('user_id','=',$user->id)
                ->where('month','=',$month)
                ->where('year','=',$year)
                ->first();
            $ball += $data->current_ball ?? 0;
        }
        return $this->count_employees != 0 ?  round(($ball / $this->count_employees),2) : 0;
    }

    public function getLongNameAttribute()
    {
       $name = $this->getAttribute('name');
        $word_count = str_word_count($name);

        if ($word_count <= 10) {
            $word = $name;  // Address fits in one line
        } else if ($word_count <= 20) {
            // Address is too long for one line, split into two lines
            $lines = explode(' ',$name, ceil($word_count/2));
            $word = implode(' ', array_slice($lines, 0, count($lines)/2)) . "\n".
                implode(' ', array_slice($lines, count($lines)/2));
        }

        return $word;
    }
}
