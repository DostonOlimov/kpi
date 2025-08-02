<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiCriteriaBand extends Model
{
    use HasFactory;

    protected $fillable = [ 'name', 'fine_ball','type'];

    public function kpi_criteria(): BelongsTo
    {
        return $this->belongsTo(KpiCriteria::class,'type','type');
    }
}
