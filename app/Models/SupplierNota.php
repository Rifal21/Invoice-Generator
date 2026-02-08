<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierNota extends Model
{
    protected $fillable = [
        'supplier_id',
        'nota_number',
        'transaction_date',
        'total_amount',
        'file_path',
        'description',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
