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

    protected $fillable = ['parent_id', 'name', 'max_score'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Kpi::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Kpi::class, 'parent_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'kpi_id');
    }
}
