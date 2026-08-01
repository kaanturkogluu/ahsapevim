<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'custom_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:25600', // Max 25MB high-res
        ]);

        $product = Product::findOrFail($request->product_id);
        
        $customImagePath = null;
        if ($request->hasFile('custom_image')) {
            $customName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $request->file('custom_image')->extension();
            $request->file('custom_image')->move(public_path('uploads/customizations'), $customName);
            $customImagePath = '/uploads/customizations/' . $customName;
        }

        $cart = session()->get('cart', []);

        // Unique cart key so custom image products don't merge with plain ones
        $cartKey = $product->id . ($customImagePath ? '_' . md5($customImagePath) : '');

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
                'custom_image' => $customImagePath
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Ürün sepete başarıyla eklendi!');
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->key])) {
            $cart[$request->key]['quantity'] = intval($request->quantity);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Sepet güncellendi.');
        }

        return redirect()->back()->with('error', 'Ürün bulunamadı.');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->key])) {
            unset($cart[$request->key]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Ürün sepetten silindi.');
        }

        return redirect()->back()->with('error', 'Ürün sepetten silinemedi.');
    }
}
