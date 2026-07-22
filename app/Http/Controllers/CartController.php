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
            // Save to storage/app/public/customizations
            $customImagePath = $request->file('custom_image')->store('customizations', 'public');
        }

        $cart = session()->get('cart', []);

        // Create a unique cart item key if it has a custom image so it doesn't merge with identical products without custom image
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

        return redirect()->back()->with('success', 'Ürün sepete başarıyla eklendi!');
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart.index', compact('cart'));
    }
}
