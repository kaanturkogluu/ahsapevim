<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $fillable = [
        'order_id',
        'to_phone',
        'message',
        'status',
        'error_message',
        'response_code',
        'type',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
