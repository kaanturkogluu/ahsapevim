<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\GoogleMerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
     * Şifre doğrulaması zorunlu.
     */
    public function syncAll(Request $request): JsonResponse
    {
        $passwordCheck = $this->verifyAdminPassword($request);
        if ($passwordCheck !== true) {
            return $passwordCheck;
        }

        try {
            $results = $this->merchantService->syncAllProducts();

            return response()->json([
                'success'       => $results['failed'] === 0,
                'message'       => "{$results['success']} ürün başarıyla gönderildi" . ($results['failed'] > 0 ? ", {$results['failed']} ürün başarısız." : '.'),
                'success_count' => $results['success'],
                'failed_count'  => $results['failed'],
                'errors'        => array_slice($results['errors'], 0, 10),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon hatası: ' . $e->getMessage(),
                'errors'  => [$e->getMessage()],
            ], 500);
        }
    }

    /**
     * Tekil ürünü Merchant Center'a gönder.
     * Şifre doğrulaması zorunlu.
     */
    public function syncProduct(Request $request, int $id): JsonResponse
    {
        $passwordCheck = $this->verifyAdminPassword($request);
        if ($passwordCheck !== true) {
            return $passwordCheck;
        }

        $product = Product::findOrFail($id);

        if (!($product->attributes['image'] ?? null)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ürünün görseli yok. Google Merchant Center görsel zorunlu kılar.',
                'errors'  => ['Ürün görseli eksik.'],
            ], 422);
        }

        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Pasif ürünler Merchant Center\'a gönderilemez.',
                'errors'  => ['Ürün pasif durumda.'],
            ], 422);
        }

        $result = $this->merchantService->insertProduct($product);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success']
                ? "'{$product->name}' başarıyla Merchant Center'a gönderildi."
                : "'{$product->name}' gönderilemedi: " . $result['message'],
            'errors'  => $result['success'] ? [] : [$result['message']],
        ], $result['success'] ? 200 : 500);
    }

    /**
     * Ürünü Merchant Center'dan sil.
     * Şifre doğrulaması zorunlu.
     */
    public function deleteFromMerchant(Request $request, int $id): JsonResponse
    {
        $passwordCheck = $this->verifyAdminPassword($request);
        if ($passwordCheck !== true) {
            return $passwordCheck;
        }

        $product = Product::findOrFail($id);
        $result  = $this->merchantService->deleteProduct($product);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success']
                ? "'{$product->name}' Merchant Center'dan silindi."
                : "Silinemedi: " . $result['message'],
            'errors'  => $result['success'] ? [] : [$result['message']],
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

    // ─────────────────────────────────────────────────────────────────────────
    // Yardımcı — Şifre Doğrulama
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * İstek içindeki 'password' alanını oturumdaki admin şifresiyle karşılaştır.
     */
    private function verifyAdminPassword(Request $request): true|JsonResponse
    {
        $password = $request->input('password', '');

        if (empty($password)) {
            return response()->json([
                'success'    => false,
                'message'    => 'Güvenlik için admin şifrenizi girmeniz gerekiyor.',
                'error_type' => 'password_required',
            ], 422);
        }

        $admin = auth()->user();

        if (!$admin || !Hash::check($password, $admin->password)) {
            return response()->json([
                'success'    => false,
                'message'    => 'Yanlış şifre. Lütfen admin şifrenizi doğru girin.',
                'error_type' => 'password_wrong',
            ], 403);
        }

        return true;
    }
}
