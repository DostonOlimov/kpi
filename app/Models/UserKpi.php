<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserKpi extends Model
{
    use SoftDeletes;

    protected $fillable = ['kpi_id', 'user_id','month','year','current_score','target_score','score_id'];

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class, 'kpi_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function score()
    {
        return $this->hasOne(Score::class,'id','score_id');
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
