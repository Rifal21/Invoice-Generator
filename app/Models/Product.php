<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'price', 'purchase_price', 'unit', 'stock', 'description'];

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
}
