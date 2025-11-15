<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;

class DashboardController extends Controller
{
    /**
     * Return dashboard overview metrics.
     *
     * Returns:
     * - products_count
     * - customers_count
     * - invoices_this_month (sales count this month)
     * - revenue_this_month (sum of sale totals this month)
     */
    public function overview(Request $request)
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $productsCount = Product::count();
        $customersCount = Customer::count();

        // allow filtering by status via query param ?status=paid|pending|all (default: paid)
        $status = $request->query('status', 'paid');

        $salesQuery = Sale::whereBetween('created_at', [$start, $end]);
        if ($status !== 'all') {
            $salesQuery = $salesQuery->where('status', $status);
        }

        // clone the query builder to run separate aggregate queries
        $invoicesThisMonth = (clone $salesQuery)->count();
        $revenueThisMonth = (float) (clone $salesQuery)->sum('total');

        return response()->json([
            'products_count' => $productsCount,
            'customers_count' => $customersCount,
            'invoices_this_month' => $invoicesThisMonth,
            'revenue_this_month' => $revenueThisMonth,
            'status_filter' => $status,
        ]);
    }
}
