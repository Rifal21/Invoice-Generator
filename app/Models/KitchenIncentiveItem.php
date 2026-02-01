<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenIncentiveItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kitchen_incentive_id',
        'description',
        'quantity',
        'unit',
        'duration_text',
        'price',
        'total_price',
    ];

    public function kitchenIncentive()
    {
        return $this->belongsTo(KitchenIncentive::class);
    }
}
