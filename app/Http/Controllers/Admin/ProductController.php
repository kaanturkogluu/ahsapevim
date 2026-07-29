<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ThreeDTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'threeDTemplate'])->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $templates = ThreeDTemplate::all();
        return view('admin.products.create', compact('categories', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255',
            'model_code' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'three_d_template_id' => 'nullable|exists:three_d_templates,id',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(10) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products'), $imageName);
            $imagePath = '/uploads/products/' . $imageName;
        }

        $features = [
            'color' => $request->color,
            'size' => $request->size,
            'images' => [] // can be extended for gallery
        ];

        Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . rand(1000, 9999),
            'price' => $request->price,
            'original_price' => $request->original_price,
            'stock' => $request->stock,
            'description' => $request->description,
            'barcode' => $request->barcode,
            'model_code' => $request->model_code,
            'image' => $imagePath,
            'three_d_template_id' => $request->three_d_template_id,
            'features' => $features,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla eklendi.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $templates = ThreeDTemplate::all();
        return view('admin.products.edit', compact('product', 'categories', 'templates'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255',
            'model_code' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:4096',
            'three_d_template_id' => 'nullable|exists:three_d_templates,id',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
            $imageName = time() . '_' . Str::random(10) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products'), $imageName);
            $imagePath = '/uploads/products/' . $imageName;
        }

        $features = $product->features ?: [];
        $features['color'] = $request->color;
        $features['size'] = $request->size;

        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . $product->id,
            'price' => $request->price,
            'original_price' => $request->original_price,
            'stock' => $request->stock,
            'description' => $request->description,
            'barcode' => $request->barcode,
            'model_code' => $request->model_code,
            'image' => $imagePath,
            'three_d_template_id' => $request->three_d_template_id,
            'features' => $features,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Ürün başarıyla silindi.');
    }
}
