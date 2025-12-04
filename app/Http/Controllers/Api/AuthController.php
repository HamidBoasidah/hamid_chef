<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ExceptionHandler, SuccessResponse;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);
            $result = $this->authService->loginApi($request->only('email', 'password'));
            return $this->successResponse($result, 'تم تسجيل الدخول بنجاح');
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

  public function logout(Request $request)
    {
        try {
            // تمرير الطلب إلى الخدمة
            $this->authService->logout($request);

            // الرد بنجاح
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الخروج بنجاح'
            ]);
        } catch (\Exception $e) {
            // تسجيل الخطأ
            Log::error('Logout error: ' . $e->getMessage(), [
                'user_id' => $request->user()?->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الخروج بنجاح' // يمكن إخفاء التفاصيل للمستخدم
            ]);
        }
    }

    public function logoutFromAllDevices(Request $request)
    {
        try {
            $this->authService->logoutFromAllDevices($request->user());
            return $this->successResponse(null, 'تم تسجيل الخروج من جميع الأجهزة بنجاح');
        } catch (\Exception $e) {
            Log::error('Logout from all devices error: ' . $e->getMessage(), [
                'user_id' => $request->user() ? $request->user()->id : null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->successResponse(null, 'تم تسجيل الخروج من جميع الأجهزة بنجاح');
        }
    }

    public function me(Request $request)
    {
        return $this->successResponse([
            'user' => $request->user()
        ], 'تم جلب بيانات المستخدم بنجاح');
    }
}
