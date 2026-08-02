<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('profile.index', ['tab' => 'siparisler']);
        }

        return view('pages.order_tracking');
    }

    public function track(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string|max:50',
            'phone_or_email' => 'required|string|max:255',
        ]);

        $code = strtoupper(trim($request->tracking_code));
        $contact = trim($request->phone_or_email);
        $cleanPhone = preg_replace('/[^0-9]/', '', $contact);

        $query = Order::with('items.product')->where(function ($q) use ($code) {
            $q->where('tracking_code', $code)
              ->orWhere('id', str_replace(['#', 'AHS-'], '', $code));
        });

        $query->where(function ($q) use ($contact, $cleanPhone) {
            $q->where('email', $contact)
              ->orWhere('phone', 'like', "%{$contact}%");
            if (!empty($cleanPhone) && strlen($cleanPhone) >= 7) {
                $q->orWhereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', '') LIKE ?", ["%{$cleanPhone}%"]);
            }
        });

        $order = $query->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Girdiğiniz sipariş takip kodu ve iletişim bilgisi ile eşleşen bir sipariş bulunamadı. Lütfen bilgilerinizi kontrol ediniz.')->withInput();
        }

        return view('pages.order_tracking', [
            'order' => $order
        ]);
    }
}
