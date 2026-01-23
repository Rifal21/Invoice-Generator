<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_order_id',
        'quantity_string',
        'item_name',
        'description',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }
}
