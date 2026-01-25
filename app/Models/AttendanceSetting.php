<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $fillable = [
        'check_in_time',
        'check_out_time',
        'office_latitude',
        'office_longitude',
        'allowed_radius',
        'require_photo',
        'require_location',
        'strict_time',
    ];

    protected $casts = [
        'require_photo' => 'boolean',
        'require_location' => 'boolean',
        'strict_time' => 'boolean',
    ];
}
