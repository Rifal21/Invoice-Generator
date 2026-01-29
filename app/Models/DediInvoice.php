<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DediInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'date',
        'customer_name',
        'total_amount',
    ];

    public function items()
    {
        return $this->hasMany(DediInvoiceItem::class);
    }
}
