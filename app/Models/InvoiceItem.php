<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice; // Added for relationship
use App\Models\Product; // Added for relationship

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'product_id', 'product_name', 'quantity', 'unit', 'price', 'total', 'description'];

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
