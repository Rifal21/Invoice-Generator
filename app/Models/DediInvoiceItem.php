<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DediInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dedi_invoice_id',
        'item_name',
        'quantity',
        'unit',
        'price',
        'total_price',
    ];

    public function invoice()
    {
        return $this->belongsTo(DediInvoice::class);
    }
}
