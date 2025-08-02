<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiCriteriaScore extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [ 'kpi_criteria_id', 'user_kpi_id','score'];

    public function kpi_criteria(): BelongsTo
    {
        return $this->belongsTo(KpiCriteria::class);
    }
}
