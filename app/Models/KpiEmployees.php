<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiEmployees extends Model
{
    protected $table = 'kpi_employee';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'kpi_dir_id',
        'work_zone_id',
        'razdel',
        'current_ball',
        'weight',
        'works_counts',
        'month',
        'status',
        'current_works',
        'band_id',
        'file_name',
        'file_path'
    ];
    /**
    * get data from kpi director with razdel
    *@param int user_id, razdel , month , year
    *@return \App\Models\Director data
    */
    public function getData(int $user_id , int $razdel){
        $data = $this::where('razdel', '=', $razdel)
            ->where('user_id', '=', $user_id)
            ->where('status', '=', 'active')
            ->get();

        return $data;
    }
    /**
     * Calculate total ball
     * @return double max_ball
     */
    public function CalculateBall()
    {
        $max_ball =0;
        $this->works_count < $this->current_works ? $max_ball = $this->weight
            : $max_ball = $this->weight * ( $this->current_works / $this->works_count);

        return $max_ball;
    }

    public function CalculatePrasent(){
        $prasent =0;
        $this->works_count < $this->current_works ? $prasent = 100
            : $prasent = floor((100 * $this->current_works) / $this->works_count);

        return $prasent;
    }
    public function CalculateDirectorBall($work_zone_id,$month)
    {
        $data = $this::where('work_zone_id','=',$work_zone_id)
            ->where('month','=',$month)
            ->whereIn('razdel',[1,2])
            ->get();
        $user = User::where('work_zone_id','=',$work_zone_id)
            ->where('role_id','=',3)
            ->get();
        $ball = 0;
        foreach ($data as $key => $item)
        {
            $ball += $item->CalculateBall()-$item->fine_ball;

        }
        return count($user) != 0 ?  round(1.25*($ball / count($user)),2) : 0;
    }

    public function kpi_dir()
    {
        return $this->belongsTo(Director::class,);
    }
    public function getCountWorksAttribute()
    {
        return $this->sum('works_count');
    }

    public function getWroksCurrentAttribute()
    {
        return $this->sum('current_works');
    }

    public function getCurrentPrasentAttribute()
    {
        $prasent =0;
        $this->works_count < $this->current_works ? $prasent = 100
            : $prasent = floor((100 * $this->current_works) / $this->works_count);

        return $prasent;

    }

    public function getMaxBallAttribute()
    {
        $max_ball =0;
        $this->works_count < $this->current_works ? $max_ball = $this->weight
            : $max_ball = $this->weight * ( $this->current_works / $this->works_count);

        return $max_ball;

    }

    protected static function boot()
    {
        parent::boot();

        $year = session('year') ?: (int)date('Y');
        $month = session('month') ?: (int)date('m');

        static::addGlobalScope(function ($query) use($year, $month) {
            $query->where('year',$year)
                ->where('month',$month);
        });
    }
}
