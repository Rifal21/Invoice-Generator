<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationReport extends Model
{
    protected $fillable = [
        'title',
        'customer_id',
        'description',
        'report_date',
        'file_path',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
