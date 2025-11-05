<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // عرض جميع الفواتير
    public function index()
    {
        $sales = Sale::with(['customer', 'items.product', 'items.color'])->latest()->get();
        return response()->json($sales);
    }

    // إنشاء فاتورة جديدة
    public function store(Request $request)

    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_color_id' => 'nullable|exists:product_colors,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // إنشاء الفاتورة بحالة غير مدفوعة
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'total' => 0,
                'status' => 'pending',
            ]);

            $total = 0;

            // إضافة تفاصيل المبيعات
            foreach ($validated['items'] as $item) {
                $subtotal = $item['unit_price'] * $item['quantity'];
                $total += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_color_id' => $item['product_color_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            // تحديث المجموع الكلي للفاتورة
            $sale->update(['total' => $total]);

            DB::commit();

            return response()->json([
                'message' => 'تم إنشاء الفاتورة بنجاح',
                'data' => $sale->load('items.product', 'items.color')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // عرض فاتورة محددة
    public function show(Sale $sale)
    {
        return response()->json($sale->load('customer', 'items.product', 'items.color'));
    }

    // تحديث حالة الفاتورة أو تعديلها
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,delivered,paid,cancelled',
        ]);

        $oldStatus = $sale->status;
        $sale->update(['status' => $validated['status']]);

        // إذا تغيرت الحالة إلى "delivered" → تصبح "paid" ويُخصم المخزون
        if ($validated['status'] === 'delivered') {
            $sale->update(['status' => 'paid']);

            foreach ($sale->items as $item) {
                if ($item->product_color_id) {
                    $color = ProductColor::find($item->product_color_id);

                    if ($color && $color->quantity >= $item->quantity) {
                        $color->decrement('quantity', $item->quantity);
                    }
                }
            }
        }

        return response()->json([
            'message' => 'تم تحديث حالة الفاتورة بنجاح',
            'data' => $sale->load('items.product', 'items.color')
        ]);
    }

    // حذف فاتورة
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return response()->json(['message' => 'تم حذف الفاتورة بنجاح']);
    }
}