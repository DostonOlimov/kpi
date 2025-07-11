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
        'user_kpi_id',
        'name',
        'description',
        'user_id',
        'year',
        'month',
        'file_path',
        'type',
        'is_completed'
    ];

      public function comments(): HasMany
      {
          return $this->hasMany(TaskComment::class);
      }

      public function scores(): HasMany
      {
          return $this->hasMany(TaskScore::class);
      }

      public function user(): BelongsTo
      {
          return $this->belongsTo(User::class, 'user_id');
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
