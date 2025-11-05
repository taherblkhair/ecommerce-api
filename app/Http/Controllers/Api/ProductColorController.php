<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    // جميع الألوان
    public function index()
    {
        return response()->json(ProductColor::with('product')->get());
    }

    // إنشاء لون جديد لمنتج
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_name' => 'required|string',
            'color_code' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'image_url' => 'nullable|string',
        ]);

        $color = ProductColor::create($validated);

        return response()->json([
            'message' => 'تمت إضافة اللون بنجاح',
            'data' => $color->load('product')
        ], 201);
    }

    // عرض لون واحد
    public function show(ProductColor $productColor)
    {
        return response()->json($productColor->load('product'));
    }

    // تحديث اللون
    public function update(Request $request, ProductColor $productColor)
    {
        $validated = $request->validate([
            'color_name' => 'sometimes|string',
            'color_code' => 'nullable|string',
            'quantity' => 'sometimes|integer|min:0',
            'image_url' => 'nullable|string',
        ]);

        $productColor->update($validated);

        return response()->json([
            'message' => 'تم تحديث اللون بنجاح',
            'data' => $productColor->load('product')
        ]);
    }

    // حذف اللون
    public function destroy(ProductColor $productColor)
    {
        $productColor->delete();
        return response()->json(['message' => 'تم حذف اللون بنجاح']);
    }
}