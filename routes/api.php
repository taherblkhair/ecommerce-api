<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductColorController;
use App\Http\Controllers\Api\CategoryControllerer;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ReportController;


Route::get('/test', function () {
    return response()->json(['message' => 'API works!']);
});

// Additional API routes can be defined here

// restful routes for products 
Route::apiResource('products', ProductController::class);

// restful routes for product colors
Route::apiResource('product_colors', ProductColorController::class);

// restful routes for categories
Route::apiResource('categories', CategoryControllerer::class);

// restful routes for sales
Route::apiResource('sales', SaleController::class);

// restful routes for customers
Route::apiResource('customers', CustomerController::class);

// route report 
Route::get('reports/overview', [ReportController::class, 'overview']);
Route::get('reports/top-products', [ReportController::class, 'topProducts']);
Route::get('reports/sales-stats', [ReportController::class, 'salesStats']);