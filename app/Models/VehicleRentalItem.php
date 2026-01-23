<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRentalItem extends Model
{
    protected $fillable = ['vehicle_rental_invoice_id', 'description', 'start_date', 'end_date', 'quantity', 'unit', 'price', 'total'];

    public function invoice()
    {
        return $this->belongsTo(VehicleRentalInvoice::class, 'vehicle_rental_invoice_id');
    }
}
