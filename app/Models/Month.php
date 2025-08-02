<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    const MONTH1 = 1;
    const MONTH2 = 2;
    const MONTH3 = 3;
    const MONTH4 = 4;
    const MONTH5 = 5;
    const MONTH6 = 6;
    const MONTH7 = 7;
    const MONTH8 = 8;
    const MONTH9 = 9;
    const MONTH10 = 10;
    const MONTH11 = 11;
    const MONTH12 = 12;

    use HasFactory;

    protected $fillable = [
        'month_id',
        'days',
        ];

    public static function getMonth($duration = null)
    {
        $arr = [
            self::MONTH1 => 'YANVAR',
            self::MONTH2 => 'FEVRAL',
            self::MONTH3 => 'MART',
            self::MONTH4 => 'APREL',
            self::MONTH5 => 'MAY',
            self::MONTH6 => 'IYUN',
            self::MONTH7 => 'IYUL',
            self::MONTH8 => 'AVGUST',
            self::MONTH9 => 'SENTYABR',
            self::MONTH10 => 'OKTYABR',
            self::MONTH11 => 'NOYABR',
            self::MONTH12 => 'DEKABR',

        ];

        if ($duration === null) {
            return $arr;
        }

        return $arr[$duration];
    }

    public static function requestMonth($request)
    {
        if($request)
        {
            if (request()->has('month_id')) {
                return $request->month_id;
            }
        }
        return (int)date('m');
    }
    public static function requestYear($request)
    {
        if($request)
        {
            if (request()->has('year')) {
                return $request->year;
            }
        }
        return (int)date('Y');
    }
    public static function getLabelsMonth()
    {
        $labels = array();
        foreach (Month::getMonth() as $item)
        {
            array_push($labels,$item);
        }
        array_push($labels,"Max ball");
        return $labels;
    }

    protected static function boot()
    {
        parent::boot();

        $year = session('year') ?: date('Y');

        static::addGlobalScope(function ($query) use($year) {
            $query->where('year',$year);
        });

    }
}
