<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineBall extends Model
{
    protected $table = 'kpi_fine_ball';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'kpi_id',
        'fine_ball',
        'order_file',
        'month_num',
        'year'
    ];
}
