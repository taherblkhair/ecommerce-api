<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // 📋 عرض جميع الزبائن
    public function index()
    {
        $customers = Customer::latest()->get();
        return response()->json($customers);
    }

    // ➕ إضافة زبون جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);        

        
        $customer = Customer::create($validated);
        

        return response()->json([
            'message' => 'تم إضافة الزبون بنجاح',
            'data' => $customer
        ], 201);
    }

    // 📄 عرض زبون محدد
    public function show(Customer $customer)
    {
        return response()->json($customer->load('sales'));
    }

    // ✏️ تحديث بيانات الزبون
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => 'تم تحديث بيانات الزبون بنجاح',
            'data' => $customer
        ]);
    }

    // ❌ حذف زبون
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'تم حذف الزبون بنجاح']);
    }
}