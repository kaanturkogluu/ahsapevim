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
        'paid_price',
        'installment',
        'merchant_payout_amount',
        'card_family',
        'card_last_four',
        'status',
        'payment_error_reason',
        'payment_id',
        'shipping_company_id',
        'cargo_tracking_code',
        'note',
        'admin_notified_at',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class, 'shipping_company_id');
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'AHS-' . strtoupper(Str::random(6));
        } while (static::where('tracking_code', $code)->exists());

        return $code;
    }
}
