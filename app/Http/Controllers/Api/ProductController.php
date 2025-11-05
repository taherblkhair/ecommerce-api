<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // عرض جميع المنتجات مع ألوانها
    public function index()
    {
        $products = Product::with('colors', 'category')->get();
        return response()->json($products);
    }

    // إنشاء منتج جديد مع الألوان
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'colors' => 'nullable|array',
            'colors.*.color_name' => 'required_with:colors|string',
            'colors.*.color_code' => 'nullable|string',
            'colors.*.quantity' => 'required_with:colors|integer|min:0',
        ]);

        // إنشاء المنتج
        $product = Product::create($validated);

        // إضافة الألوان إن وُجدت
        if (!empty($validated['colors'])) {
            foreach ($validated['colors'] as $colorData) {
                $product->colors()->create($colorData);
            }
        }

        return response()->json([
            'message' => 'تم إنشاء المنتج بنجاح',
            'data' => $product->load('colors')
        ], 201);
    }

    // عرض منتج محدد
    public function show(Product $product)
    {
        return response()->json($product->load('colors', 'category'));
    }

    // تحديث المنتج
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'تم تحديث المنتج بنجاح',
            'data' => $product->load('colors', 'category')
        ]);
    }

    // حذف المنتج
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'تم حذف المنتج بنجاح']);
    }
}