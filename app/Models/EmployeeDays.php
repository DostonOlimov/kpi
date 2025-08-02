<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDays extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'month_id',
        'year',
        'days'
    ];

    protected static function boot()
    {
        parent::boot();

        $year = session('year') ?: (int)date('Y');
        $month = session('month') ?: (int)date('m');

        static::addGlobalScope(function ($query) use($year, $month) {
            $query->where('year',$year)
                ->where('month_id',$month);
        });
    }
}
