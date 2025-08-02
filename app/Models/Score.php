<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Model
{
    use SoftDeletes;

    const SCORE_BY_AI = 1;
    const SCORE_BY_DIRECTOR = 2;
    const SCORE_BY_MANAGER = 3;
    const SCORE_BY_BOSS = 4;

    protected $table = 'scores';

    protected $fillable = [
        'id',
        'user_kpi_id',
        'score',
        'type',
        'feedback',
        'is_active',
        'scored_by',
        'ai_extracted_text',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
