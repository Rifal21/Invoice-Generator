<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\LogsActivity;

    protected $fillable = ['category_id', 'supplier_id', 'name', 'price', 'purchase_price', 'unit', 'stock', 'description', 'image'];

    protected $casts = [
        'price' => 'float',
        'purchase_price' => 'float',
        'stock' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
