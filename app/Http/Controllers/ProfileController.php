<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Oturum açan kullanıcının e-posta adresiyle eşleşen misafir/eski siparişleri kullanıcıya bağla
        if (!empty($user->email)) {
            \App\Models\Order::whereNull('user_id')
                ->where('email', $user->email)
                ->update(['user_id' => $user->id]);
        }

        $orders = \App\Models\Order::where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if (!empty($user->email)) {
                $q->orWhere('email', $user->email);
            }
        })->with('items.product')->latest()->get();

        $favorites = $user->favoriteProducts()->latest()->get();

        return view('profile.index', compact('user', 'orders', 'favorites'));
    }

    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profile.index', ['tab' => 'bilgiler'])->with('success', 'Kullanıcı bilgileriniz başarıyla güncellendi.');
    }

    public function updateAddress(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
        ]);

        $user->update([
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
        ]);

        return redirect()->route('profile.index', ['tab' => 'adres'])->with('success', 'Adres bilgileriniz başarıyla kaydedildi.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.index', ['tab' => 'sifre'])->with('success', 'Şifreniz başarıyla değiştirildi.');
    }

    public function cancelOrder($id)
    {
        $order = \App\Models\Order::where('id', $id)
                    ->where('user_id', auth()->id())
                    ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Sipariş bulunamadı veya bu işlem için yetkiniz yok.');
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Yalnızca ödeme bekleyen siparişler iptal edilebilir.');
        }

        // Restore stock for each item
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('profile.index', ['tab' => 'siparisler'])->with('success', 'Sipariş #' . $order->id . ' başarıyla iptal edildi.');
    }
}
