<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiDirector extends Model
{
    protected $table = 'kpi_director';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'work_zone_id',
        'razdel',
        'current_ball',
        'weight',
        'works_counts',
        'month',
        'status',
        'current_works',
        'taken_works',
        'band_id',
    ];
    
}
