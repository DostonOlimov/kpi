<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiCriteria extends Model
{
    use HasFactory;

    protected $fillable = ['kpi_id', 'name', 'description','type'];

    public function kpi(): BelongsTo
    {
        return $this->belongsTo(Kpi::class);
    }
    public function bands(): HasMany
    {
        return $this->hasMany(KpiCriteriaBand::class,'type','type');
    }
    public function scores(): HasMany
    {
        return $this->hasMany(KpiCriteriaScore::class);
    }
}
