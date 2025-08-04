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
        'new_total',
        'days'
    ];
    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
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
