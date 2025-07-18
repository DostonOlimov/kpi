<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kpi extends Model
{
    use HasFactory;

    CONST SELF_BY_PERSON = 1;
    CONST BEHAVIOUR = 2;
    CONST ACTIVITY = 3;
    CONST IJRO = 4;
    CONST PERMANENT = 5;

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

    public function criterias(): HasMany
    {
        return $this->hasMany(KpiCriteria::class);
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
