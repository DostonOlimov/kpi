<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_id',
        'name',
        'department',
        'date',
        'first_in',
        'last_out',
        'status',
        'comment',
        'created_by',
    ];

    /**
     * Get the user that owns the attendance record.
     * Relation: attendances.external_id -> users.ch_id
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'external_id', 'ch_id');
    }

    /**
     * Get the display status for the attendance.
     * Priority: custom status > calculated status (Bor/Yo'q/Sababli)
     * 
     * @return string
     */
    public function getDisplayStatusAttribute()
    {
        // If custom status is set and not null, use it
        if ($this->status) {
            return $this->status;
        }

        // Calculate status based on attendance data
        $hasAttendance = $this->first_in || $this->last_out;
        
        if (!$hasAttendance) {
            return 'Yo\'q';
        }

        return 'Bor';
    }

    /**
     * Check if the attendance is late (after 9:00).
     * 
     * @return bool
     */
    public function getIsLateAttribute()
    {
        if (!$this->first_in) {
            return false;
        }
        
        $firstInTime = date('H:i', strtotime($this->first_in));
        return $firstInTime > '09:00';
    }

    /**
     * Check if early departure (before 18:00).
     * 
     * @return bool
     */
    public function getIsEarlyAttribute()
    {
        if (!$this->last_out) {
            return false;
        }
        
        $lastOutTime = date('H:i', strtotime($this->last_out));
        return $lastOutTime < '18:00';
    }

    /**
     * Get status class for badge styling.
     * 
     * @return string
     */
    public function getStatusClassAttribute()
    {
        $displayStatus = $this->display_status;
        
        if ($displayStatus === 'Bor') {
            return 'badge-success';
        } elseif ($displayStatus === 'Yo\'q') {
            return 'badge-danger';
        } else {
            return 'badge-warning'; // For custom statuses like 'Sababli'
        }
    }
}
