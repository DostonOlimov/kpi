<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KpiEmployees;

class Director extends Model
{
    protected $table = 'kpi_director';
    public $timestamps = true;
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'work_zone_id',
        'razdel',
        'weight',
        'current_ball',
        'works_count',
        'month',
        'status',
        'current_works',
        'band_id',
        'has_lock',
        'taken_works'
    ];
 /**
  * get data from kpi director with razdel
  *@param int razdel , month , year
  *@return \App\Models\Director data
  */
    public function getData(int $razdel,int $month , int $year){
        $data = $this::where('razdel', '=', $razdel)
            ->where('status', '=', 'active')
            ->where('month','=', $month)
            ->where('year','=',$year)
            ->get();

        return $data;
    }
     /**
  * get data from kpi director with razdel
  *@param int user_id , year
  *@return array arr
  */
  public function getTextMonthsName(int $user_id,int $year){
    $data = $this::where('razdel', '=', 1)
        ->where('status', '=', 'active')
        ->where('user_id','=', $user_id)
        ->where('year','=',$year)
        ->get();
    $arr = [];
        foreach ($data as $value){
            $arr[] = $value->month;
        }
    return array_unique($arr);
}

    public function kpi_employee()
    {
        return $this->hasMany(KpiEmployees::class,'kpi_dir_id','id');
    }

    public function getCountWorksAttribute()
    {
        return $this->kpi_employee()->sum('works_count');
    }

    public function getCurrentWorksAttribute()
    {
        return $this->kpi_employee()->sum('current_works');
    }

    public function getCurrentPrasentAttribute()
    {
        $ijro = 0;
        $count_works = $this->kpi_employee()->sum('works_count');
        $curent_works = $this->kpi_employee()->sum('current_works');
        if($count_works != 0){
            $curent_works > $count_works ? $ijro = 100 : $ijro = $curent_works / $count_works * 100;
        }
        return $ijro;
       
    }
}
