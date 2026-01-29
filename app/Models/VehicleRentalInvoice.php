<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRentalInvoice extends Model
{
    use \App\Traits\LogsActivity;

    protected $fillable = ['invoice_number', 'date', 'customer_name', 'total_amount'];

    public function items()
    {
        return $this->hasMany(VehicleRentalItem::class);
    }
}
