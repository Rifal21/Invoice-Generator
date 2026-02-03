<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice; // Added for relationship
use App\Models\Product; // Added for relationship

class InvoiceItem extends Model
{
    use \App\Traits\LogsActivity;

    protected $fillable = ['invoice_id', 'product_id', 'product_name', 'quantity', 'unit', 'price', 'purchase_price', 'total', 'description'];

    protected $casts = [
        'quantity' => 'float',
        'price' => 'float',
        'purchase_price' => 'float',
        'total' => 'float',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    //
}
