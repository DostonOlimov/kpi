<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kpi extends Model
{
    use HasFactory;

    CONST TYPE_1 = 1;
    CONST TYPE_2 = 2;
    CONST TYPE_3 = 3;
    CONST TYPE_4 = 4;

    protected $fillable = ['parent_id', 'name', 'max_score','user_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Kpi::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Kpi::class, 'parent_id');
    }

    public function user_kpis(): HasMany
    {
        return $this->hasMany(UserKpi::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_kpis')
            ->withPivot('target_score', 'current_score')
            ->withTimestamps();
    }

    public function isCategory()
    {
        return is_null($this->max_score);
    }

    public function scopeCategories($query)
    {
        return $query->whereNull('max_score');
    }

    public function scopeKpis($query)
    {
        return $query->whereNotNull('max_score');
    }
}
