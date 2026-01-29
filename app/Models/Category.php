<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use \App\Traits\LogsActivity;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    //
}
