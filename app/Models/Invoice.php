<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\InvoiceItem;

class Invoice extends Model
{
    use \App\Traits\LogsActivity, SoftDeletes;

    protected $fillable = ['invoice_number', 'date', 'customer_name', 'total_amount', 'discount', 'is_custom', 'whatsapp_sent_at', 'telegram_sent_at'];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    //
}
