<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\R2StorageService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'custom_image_front' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:35840',
            'custom_image_back' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:35840',
            'custom_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:35840',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Stock check
        if ($product->stock <= 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Bu ürün şu anda stokta bulunmamaktadır.'], 422);
            }
            return redirect()->back()->with('error', 'Üzgünüz, bu ürün şu anda stokta bulunmamaktadır.');
        }

        $hasFront = $request->hasFile('custom_image_front');
        $hasBack = $request->hasFile('custom_image_back');
        $hasSingle = $request->hasFile('custom_image');
        $hasPreview = $request->filled('custom_preview_base64');

        // Strict Security Check: 1st Photo is MANDATORY
        if (!$hasFront && !$hasSingle) {
            return redirect()->back()->with('error', 'Sipariş verebilmek için 1. Fotoğrafı (Ön Yüz) yüklemeniz zorunludur!');
        }

        // Strict Filename & Double Extension Verification (anti-exploit / security protection)
        $dangerList = ['exe', 'php', 'zip', 'rar', 'sh', 'bat', 'py', 'js', 'html', 'htm', 'phtml', 'phps', 'jar', 'vbs', 'scr', 'dll', 'cmd'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        foreach (['custom_image_front', 'custom_image_back', 'custom_image'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $origName = strtolower($file->getClientOriginalName());
                $nameParts = explode('.', $origName);
                $lastExt = end($nameParts);

                // Reject if final extension is not in allowed list OR if any part contains dangerous extensions
                if (!in_array($lastExt, $allowedExts) || count(array_intersect($nameParts, $dangerList)) > 0) {
                    $err = 'Güvenlik uyarısı: Yüklemek istediğiniz dosya şüpheli veya zararlı çift uzantı (örn: .exe, .php) barındırıyor!';
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['status' => 'error', 'message' => $err], 422);
                    }
                    return redirect()->back()->with('error', $err);
                }
            }
        }

        // Handle Front Image (R2 - Orijinal kalite ve boyut %100 korunarak tam dosya yüklenir)
        $frontImagePath = null;
        if ($hasFront) {
            $frontImagePath = R2StorageService::uploadRaw($request->file('custom_image_front'), 'customizations', 'front');
        }

        // Handle Back Image (R2 - Orijinal kalite ve boyut %100 korunarak tam dosya yüklenir)
        $backImagePath = null;
        if ($hasBack) {
            $backImagePath = R2StorageService::uploadRaw($request->file('custom_image_back'), 'customizations', 'back');
        }

        // Fallback Single Custom Image (R2 - Orijinal kalite ve boyut %100 korunarak tam dosya yüklenir)
        $singleImagePath = null;
        if ($hasSingle) {
            $singleImagePath = R2StorageService::uploadRaw($request->file('custom_image'), 'customizations', 'custom');
        }

        $isGift = $request->boolean('is_gift');
        $giftNote = trim($request->input('gift_note', ''));

        $cart = session()->get('cart', []);

        $uniqueSeed = ($frontImagePath ?: '') . ($backImagePath ?: '') . ($singleImagePath ?: '') . ($giftNote ?: '');
        $cartKey = $product->id . ($uniqueSeed ? '_' . md5($uniqueSeed) : '');

        $displayImage = $frontImagePath 
            ?: ($singleImagePath ?: $product->image);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $displayImage,
                'custom_image_front' => $frontImagePath ?: ($singleImagePath ?: null),
                'custom_image_back' => $backImagePath ?: null,
                'custom_image' => $frontImagePath ?: ($singleImagePath ?: null),
                'custom_preview' => null,
                'is_gift' => $isGift,
                'gift_note' => $giftNote,
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Kişiselleştirilmiş ürününüz sepete eklendi!',
                'cart' => $cart,
                'count' => count($cart),
                'total' => $this->calculateTotal($cart),
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Kişiselleştirilmiş ürününüz sepete eklendi!');
    }

    public function index()
    {
        return redirect()->to(url('/urunler?open_cart=1'));
    }

    public function getCartData()
    {
        $cart = session()->get('cart', []);
        return response()->json([
            'status' => 'success',
            'cart' => $cart,
            'count' => count($cart),
            'total' => $this->calculateTotal($cart),
        ]);
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

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sepet güncellendi.',
                    'cart' => $cart,
                    'count' => count($cart),
                    'total' => $this->calculateTotal($cart),
                ]);
            }

            return redirect()->back()->with('success', 'Sepet güncellendi.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'error', 'message' => 'Ürün bulunamadı.'], 404);
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

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ürün sepetten silindi.',
                    'cart' => $cart,
                    'count' => count($cart),
                    'total' => $this->calculateTotal($cart),
                ]);
            }

            return redirect()->back()->with('success', 'Ürün sepetten silindi.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'error', 'message' => 'Ürün sepetten silinemedi.'], 400);
        }

        return redirect()->back()->with('error', 'Ürün sepetten silinemedi.');
    }

    private function calculateTotal($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += ($item['price'] * $item['quantity']);
        }
        return number_format($total, 2, ',', '.') . ' TL';
    }
}
