<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InvoiceItem;

class Invoice extends Model
{
    protected $fillable = ['invoice_number', 'date', 'customer_name', 'total_amount', 'whatsapp_sent_at', 'telegram_sent_at'];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    //
}
