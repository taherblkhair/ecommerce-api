<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // 📊 تقرير شامل للوحة التحكم
    public function overview()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $totalSales = Sale::where('status', 'paid')->sum('total');
        $todaySales = Sale::where('status', 'paid')->whereDate('created_at', $today)->sum('total');
        $monthSales = Sale::where('status', 'paid')->whereBetween('created_at', [$monthStart, now()])->sum('total');

        $totalCustomers = Customer::count();
        $totalProducts = Product::count();

        return response()->json([
            'summary' => [
                'total_sales' => $totalSales,
                'today_sales' => $todaySales,
                'month_sales' => $monthSales,
                'total_customers' => $totalCustomers,
                'total_products' => $totalProducts,
            ]
        ]);
    }

    // 🏆 أكثر المنتجات مبيعًا
    public function topProducts()
    {
        $topProducts = SaleItem::select(
                'product_id',
                DB::raw('SUM(quantity) as total_sold'),
                DB::raw('SUM(quantity * unit_price) as revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(10)
            ->get();

        return response()->json($topProducts);
    }

    // 💰 إحصائيات المبيعات حسب الحالة
    public function salesStats()
    {
        $stats = Sale::select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->get();

        return response()->json($stats);
    }
}