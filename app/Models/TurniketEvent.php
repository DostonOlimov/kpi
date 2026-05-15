<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Raw access-control event coming from a Hikvision turniket.
 *
 * Two physical devices feed this table:
 *   - port 8002 → direction = 'in'  (check-in)
 *   - port 8003 → direction = 'out' (check-out)
 */
class TurniketEvent extends Model
{
    use HasFactory;

    public const DIRECTION_IN  = 'in';
    public const DIRECTION_OUT = 'out';

    protected $fillable = [
        'port',
        'direction',
        'external_id',
        'name',
        'user_type',
        'serial_no',
        'event_time',
        'event_date',
        'event_clock',
        'major',
        'minor',
        'door_no',
        'card_reader_no',
        'card_type',
        'verify_mode',
        'mask',
        'picture_url',
        'raw',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'event_date' => 'date',
        'raw'        => 'array',
    ];

    /**
     * Link back to the user via the same ch_id ↔ external_id mapping
     * already used by Attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'external_id', 'ch_id');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('event_date', $date);
    }

    public function scopeIn($query)
    {
        return $query->where('direction', self::DIRECTION_IN);
    }

    public function scopeOut($query)
    {
        return $query->where('direction', self::DIRECTION_OUT);
    }
}
