<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

      protected $fillable = [
        'user_kpi_id',
        'name',
        'description',
        'type',
        'file_path',
        'extracted_text',
        'score',
        'task_score_id'
    ];

      public function comments(): HasMany
      {
          return $this->hasMany(TaskComment::class);
      }

      public function scores(): HasMany
      {
          return $this->hasMany(TaskScore::class);
      }

      public function task_score(): HasOne
      {
          return $this->hasOne(TaskScore::class,'id','task_score_id');
      }

      public function user_kpi(): BelongsTo
      {
          return $this->belongsTo(UserKpi::class);
      }
}
