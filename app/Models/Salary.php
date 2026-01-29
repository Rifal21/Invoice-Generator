<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use \App\Traits\LogsActivity;

    protected $fillable = [
        'user_id',
        'period',
        'start_date',
        'end_date',
        'daily_salary',
        'working_days',
        'base_salary',
        'bonus',
        'deductions',
        'net_salary',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'period' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'paid_at' => 'datetime',
        'daily_salary' => 'float',
        'base_salary' => 'float',
        'bonus' => 'float',
        'deductions' => 'float',
        'net_salary' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
