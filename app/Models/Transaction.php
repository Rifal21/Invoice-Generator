<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'status',
        'payment_channel',
        'balance_before',
        'balance_after',
        'description',
        'reference_id',
        'snap_token',
        'payment_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
