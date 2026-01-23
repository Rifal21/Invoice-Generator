<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiceDeliveryItem extends Model
{
    protected $fillable = ['rice_delivery_id', 'quantity_string', 'description', 'price', 'total'];

    public function delivery()
    {
        return $this->belongsTo(RiceDelivery::class, 'rice_delivery_id');
    }
}
