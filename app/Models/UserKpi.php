<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserKpi extends Model
{
    use SoftDeletes;

    // Status constants for better maintainability
    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    // Status translations mapping
    public const STATUS_TRANSLATIONS = [
        self::STATUS_NEW => 'Yangi',
        self::STATUS_IN_PROGRESS => 'Jarayonda',
        self::STATUS_COMPLETED => 'Bajarildi',
    ];

    protected $fillable = ['kpi_id', 'user_id','month','year','current_score','target_score','score_id', 'status'];

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
    /**
     * Get the translated status name.
     *
     * @return string
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUS_TRANSLATIONS[$this->status] ?? 'Noma\'lum holat';
    }

    /**
     * Get all available statuses.
     *
     * @return array
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * Get status translations for dropdowns/forms.
     *
     * @return array
     */
    public static function getStatusOptions(): array
    {
        return self::STATUS_TRANSLATIONS;
    }

    /**
     * Scope to filter by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if the KPI is new.
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * Check if the KPI is in progress.
     *
     * @return bool
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if the KPI is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
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
