<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeKpiResult extends Model
{
    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_CALCULATED = 'calculated';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // Status translations
    public const STATUS_TRANSLATIONS = [
        self::STATUS_PENDING => 'Kutilmoqda',
        self::STATUS_CALCULATED => 'Hisoblandi',
        self::STATUS_APPROVED => 'Tasdiqlandi',
        self::STATUS_REJECTED => 'Rad etildi',
    ];

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'total_score',
        'final_score',
        'grade',
        'status',
        'comments',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'total_score' => 'decimal:2',
        'final_score' => 'decimal:2',
        'evaluated_at' => 'datetime',
    ];

    /**
     * Get the user (employee) this result belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the evaluator user.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Get the translated status name.
     */
    public function getStatusNameAttribute(): string
    {
        return self::STATUS_TRANSLATIONS[$this->status] ?? 'Noma\'lum holat';
    }

    /**
     * Get all available statuses.
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_CALCULATED,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];
    }

    /**
     * Get status translations for dropdowns/forms.
     */
    public static function getStatusOptions(): array
    {
        return self::STATUS_TRANSLATIONS;
    }

    /**
     * Scope to filter by year and month.
     */
    public function scopeByPeriod($query, $year, $month = null)
    {
        $query->where('year', $year);
        
        if ($month) {
            $query->where('month', $month);
        }
        
        return $query;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if result is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if result is calculated.
     */
    public function isCalculated(): bool
    {
        return $this->status === self::STATUS_CALCULATED;
    }

    /**
     * Check if result is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if result is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Mark as calculated.
     */
    public function markAsCalculated(): void
    {
        $this->update(['status' => self::STATUS_CALCULATED]);
    }

    /**
     * Mark as approved.
     */
    public function markAsApproved($evaluatorId = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'evaluated_by' => $evaluatorId ?? auth()->id(),
            'evaluated_at' => now(),
        ]);
    }

    /**
     * Mark as rejected.
     */
    public function markAsRejected($evaluatorId = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'evaluated_by' => $evaluatorId ?? auth()->id(),
            'evaluated_at' => now(),
        ]);
    }

    /**
     * Boot method for global scopes.
     */
    protected static function boot()
    {
        parent::boot();

        $year = session('year') ?: (int)date('Y');
        $month = session('month') ?: (int)date('m');

        static::addGlobalScope(function ($query) use ($year, $month) {
            $query->where('year', $year)
                  ->where('month', $month);
        });
    }
}
