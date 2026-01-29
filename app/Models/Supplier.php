<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use \App\Traits\LogsActivity;

    protected $fillable = ['name', 'email', 'phone', 'address', 'contact_person'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
