<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $fillable = [
        'order_id',
        'to_email',
        'subject',
        'body',
        'status',
        'error_message',
        'type',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
