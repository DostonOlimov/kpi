<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugalterSumma extends Model
{
    protected $table = 'bugalter_summa';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'summa',
        'month',
        'status'
    ];
}
