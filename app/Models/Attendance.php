<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'notes',
        // GPS Location
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        // Photo verification
        'check_in_photo',
        'check_out_photo',
        // IP & Device tracking
        'check_in_ip',
        'check_out_ip',
        'check_in_user_agent',
        'check_out_user_agent',
        'check_in_device_id',
        'check_out_device_id',
        // Distance tracking
        'check_in_distance',
        'check_out_distance',
        // Approval system
        'is_manual_entry',
        'approved_by',
        'approved_at',
        'correction_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
        'approved_at' => 'datetime',
        'is_manual_entry' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if attendance is within allowed location radius
     */
    public function isWithinAllowedRadius(): bool
    {
        if (!$this->check_in_distance) {
            return false;
        }

        $settings = AttendanceSetting::first();
        return $this->check_in_distance <= ($settings->allowed_radius ?? 100);
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'present' => 'green',
            'late' => 'yellow',
            'absent' => 'red',
            default => 'gray',
        };
    }
}
