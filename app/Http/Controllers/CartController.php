<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'custom_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:25600', // Max 25MB high-res
        ]);

        $product = Product::findOrFail($request->product_id);
        
        if (!$request->hasFile('custom_image') && !$request->filled('custom_preview_base64')) {
            return redirect()->back()->with('error', 'Lütfen çerçeve içerisine yerleştirilecek bir fotoğraf yükleyiniz!');
        }

        $customImagePath = null;
        if ($request->hasFile('custom_image')) {
            $customName = time() . '_' . Str::random(10) . '.' . $request->file('custom_image')->extension();
            $request->file('custom_image')->move(public_path('uploads/customizations'), $customName);
            $customImagePath = '/uploads/customizations/' . $customName;
        }

        // Handle 3D canvas snapshot base64 image
        $customPreviewPath = null;
        if ($request->filled('custom_preview_base64')) {
            $base64Data = $request->custom_preview_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $data = base64_decode($data);
                if ($data !== false) {
                    $previewName = '3d_preview_' . time() . '_' . Str::random(8) . '.png';
                    File::ensureDirectoryExists(public_path('uploads/customizations'));
                    file_put_contents(public_path('uploads/customizations/' . $previewName), $data);
                    $customPreviewPath = '/uploads/customizations/' . $previewName;
                }
            }
        }

        $cart = session()->get('cart', []);

        // Unique cart key so custom image products don't merge with plain ones
        $uniqueSeed = ($customPreviewPath ?: '') . ($customImagePath ?: '');
        $cartKey = $product->id . ($uniqueSeed ? '_' . md5($uniqueSeed) : '');

        $displayImage = $customPreviewPath 
            ? url($customPreviewPath) 
            : ($customImagePath ? url($customImagePath) : $product->image);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $displayImage,
                'custom_image' => $customImagePath ? url($customImagePath) : null,
                'custom_preview' => $customPreviewPath ? url($customPreviewPath) : null,
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
