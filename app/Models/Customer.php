<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use \App\Traits\LogsActivity;

    protected $fillable = ['name', 'email', 'phone', 'pic', 'telegram_chat_id', 'address', 'bank_account_info', 'description'];
}
