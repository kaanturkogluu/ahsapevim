<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProductToMerchant;
use App\Models\Product;
use App\Services\GoogleMerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    public function __construct(protected GoogleMerchantService $merchantService)
    {
    }

    /**
     * Merchant Center yönetim sayfası.
     */
    public function index()
    {
        $products    = Product::with('category')->ordered()->get();
        $isConnected = false;
        $accountName = null;
        $error       = null;

        try {
            $status      = $this->merchantService->testConnection();
            $isConnected = $status['success'];
            $accountName = $status['name'] ?? null;
            if (!$isConnected) {
                $error = $status['message'] ?? 'Bağlantı başarısız.';
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return view('admin.merchant.index', compact(
            'products',
            'isConnected',
            'accountName',
            'error'
        ));
    }

    /**
     * Tüm aktif ürünleri Merchant Center'a senkronize et.
     */
    public function syncAll(): RedirectResponse
    {
        try {
            $results = $this->merchantService->syncAllProducts();

            $message = "Senkronizasyon tamamlandı: {$results['success']} başarılı, {$results['failed']} başarısız.";

            if (!empty($results['errors'])) {
                $errorList = implode('<br>', array_slice($results['errors'], 0, 5));
                return redirect()->route('admin.merchant.index')
                    ->with('warning', $message . '<br><small>' . $errorList . '</small>');
            }

            return redirect()->route('admin.merchant.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.merchant.index')
                ->with('error', 'Hata: ' . $e->getMessage());
        }
    }

    /**
     * Tekil ürünü Merchant Center'a gönder (async queue ile).
     */
    public function syncProduct(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        if (!$product->image) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ürünün görseli yok. Merchant Center görsel zorunlu kılar.',
            ], 422);
        }

        SyncProductToMerchant::dispatch($product, 'insert');

        return response()->json([
            'success' => true,
            'message' => "'{$product->name}' kuyruğa eklendi. Kısa süre içinde senkronize edilecek.",
        ]);
    }

    /**
     * Ürünü Merchant Center'dan sil.
     */
    public function deleteFromMerchant(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        SyncProductToMerchant::dispatch($product, 'delete');

        return response()->json([
            'success' => true,
            'message' => "'{$product->name}' Merchant Center'dan silinmek üzere kuyruğa eklendi.",
        ]);
    }

    /**
     * Merchant Center API bağlantı durumu (JSON).
     */
    public function status(): JsonResponse
    {
        try {
            $result = $this->merchantService->testConnection();
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tek ürünün Merchant Center durumunu sorgula (JSON).
     */
    public function productStatus(int $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        try {
            $status = $this->merchantService->getProductStatus($product);

            if (!$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu ürün Merchant Center\'da bulunamadı.',
                ]);
            }

            return response()->json(['success' => true, 'data' => $status]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
