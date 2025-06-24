<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Razdel extends Model
{
    protected $table = 'kpi_razdel';
    use HasFactory;
    protected $fillable = [
        'name',
        'weight'
    ];
}
