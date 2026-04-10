<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Edodocument extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'edo_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_number',
        'document_date',
        'document_type',
        'due_date',
        'sender',
        'task_created_at',
        'summary',
        'status',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'document_date' => 'date',
        'due_date' => 'date',
        'task_created_at' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Complete the document and set appropriate status based on completion time.
     *
     * @return void
     */
    public function markAsCompleted()
    {
        $completedAt = Carbon::now();
        $dueDate = Carbon::parse($this->due_date);
        
        // Determine status based on completion time vs due date
        if ($completedAt->gt($dueDate)) {
            // Completed after due date
            $this->status = 'muddati_o_tib_bajarilgan';
        } else {
            // Completed on or before due date
            $this->status = 'vaqtida_bajarilgan';
        }
        
        $this->completed_at = $completedAt;
        $this->save();
    }

    /**
     * Get formatted status for display.
     *
     * @return string
     */
    public function getStatusDisplayAttribute()
    {
        $statusMap = [
            'pending' => 'Kutilmoqda',
            'bajarildi' => 'Bajarildi',
            'vaqtida_bajarilgan' => 'Vaqtida bajarilgan',
            'muddati_o_tib_bajarilgan' => 'Muddati o\'tib bajarilgan',
            'in_progress' => 'Jarayonda',
        ];
        
        return $statusMap[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        $badgeClasses = [
            'pending' => 'badge-secondary',
            'bajarildi' => 'badge-success',
            'vaqtida_bajarilgan' => 'badge-success',
            'muddati_o_tib_bajarilgan' => 'badge-danger',
            'in_progress' => 'badge-warning',
        ];
        
        return $badgeClasses[$this->status] ?? 'badge-secondary';
    }

    /**
     * Check if document is overdue.
     *
     * @return bool
     */
    public function isOverdue()
    {
        if ($this->status && in_array($this->status, ['vaqtida_bajarilgan', 'muddati_o_tib_bajarilgan'])) {
            return false;
        }
        
        return Carbon::now()->gt(Carbon::parse($this->due_date));
    }
}
