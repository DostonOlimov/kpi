<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

      protected $fillable = [
        'kpi_id',
        'name',
        'description',
        'user_id',
        'year',
        'month',
        'file_path',
        'type',
        'is_completed'
    ];

}
