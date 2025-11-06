<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * تسجيل الدخول وإنشاء توكن API
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'بيانات الاعتماد غير صحيحة'
            ], 401);
        }

        // حذف التوكنات القديمة (اختياري)
        $user->tokens()->delete();

        // إنشاء توكن جديد
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * تسجيل الخروج وحذف التوكنات
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }
}