<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Favorilerinizi görüntülemek için lütfen giriş yapın.');
        }

        $favorites = Auth::user()->favoriteProducts()->with('category')->latest('favorites.created_at')->get();

        return view('favorites.index', compact('favorites'));
    }

    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'Favorilere eklemek için lütfen giriş yapın veya Google ile bağlanın.',
                'redirect' => route('login'),
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;

        $favorite = Favorite::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($favorite) {
            $favorite->delete();
            $action = 'removed';
            $message = 'Ürün favorilerinizden çıkarıldı.';
        } else {
            Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $action = 'added';
            $message = 'Ürün favorilerinize eklendi!';
        }

        $count = Auth::user()->favorites()->count();

        return response()->json([
            'status' => 'success',
            'action' => $action,
            'count' => $count,
            'message' => $message,
        ]);
    }
}
