<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class HomeBannerController extends Controller
{
    public function index()
    {
        $banners = collect();
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('home_banners')) {
                $banners = HomeBanner::orderBy('order', 'asc')->get();
            }
        } catch (\Throwable $e) {
            $banners = collect();
        }
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'title' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            File::ensureDirectoryExists(public_path('uploads/banners'));
            $imageName = 'banner_' . time() . '_' . Str::random(8) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $imagePath = '/uploads/banners/' . $imageName;
        }

        $maxOrder = HomeBanner::max('order') ?? 0;

        HomeBanner::create([
            'title' => $request->title ?: 'Anasayfa Görseli',
            'image' => $imagePath,
            'order' => $request->filled('order') ? (int) $request->order : ($maxOrder + 1),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Anasayfa görseli başarıyla eklendi.');
    }

    public function update(Request $request, $id)
    {
        $banner = HomeBanner::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'order' => 'required|integer|min:0',
        ]);

        $imagePath = $banner->image;
        if ($request->hasFile('image')) {
            if ($banner->image && !str_contains($banner->image, '/images/a') && File::exists(public_path($banner->image))) {
                File::delete(public_path($banner->image));
            }
            File::ensureDirectoryExists(public_path('uploads/banners'));
            $imageName = 'banner_' . time() . '_' . Str::random(8) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $imagePath = '/uploads/banners/' . $imageName;
        }

        $banner->update([
            'title' => $request->title ?: 'Anasayfa Görseli',
            'image' => $imagePath,
            'order' => (int) $request->order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Anasayfa görseli güncellendi.');
    }

    public function destroy($id)
    {
        $banner = HomeBanner::findOrFail($id);

        // Varsayılan a1-a6 görselleri haricindeki yüklenen dosyaları sil
        if ($banner->image && !str_contains($banner->image, '/images/a') && File::exists(public_path($banner->image))) {
            File::delete(public_path($banner->image));
        }

        $banner->delete();

        return redirect()->back()->with('success', 'Görsel silindi.');
    }
}
