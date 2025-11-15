<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductColorController;
use App\Http\Controllers\Api\CategoryControllerer;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


// Additional API routes can be defined here
Route::middleware('auth:sanctum')->group(function () {

// logout route
Route::post('logout', [AuthController::class, 'logout']);

//restful routes for categories
Route::apiResource('categories', CategoryControllerer::class);

// restful routes for products
Route::apiResource('products', ProductController::class);

// restful routes for product colors
Route::apiResource('product_colors', ProductColorController::class);

// restful routes for categories

// restful routes for sales
Route::apiResource('sales', SaleController::class);

// restful routes for customers
Route::apiResource('customers', CustomerController::class);

// route report
Route::get('reports/overview', [ReportController::class, 'overview']);
Route::get('reports/top-products', [ReportController::class, 'topProducts']);
Route::get('reports/sales-stats', [ReportController::class, 'salesStats']);

// dashboard overview
Route::get('dashboard/overview', [DashboardController::class, 'overview']);


});
