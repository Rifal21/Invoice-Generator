<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiceDelivery extends Model
{
    protected $fillable = ['nota_number', 'location', 'date', 'customer_name', 'total_amount'];

    public function items()
    {
        return $this->hasMany(RiceDeliveryItem::class);
    }
}
