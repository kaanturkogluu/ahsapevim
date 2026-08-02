<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'tracking_code',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'district',
        'identity_number',
        'total_amount',
        'status',
        'payment_id'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'AHS-' . strtoupper(Str::random(6));
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }
}
