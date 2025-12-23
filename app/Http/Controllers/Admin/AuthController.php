<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
//use App\Http\Traits\ExceptionHandler;
use App\Http\Traits\SuccessResponse;
// logging removed
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{
    //use ExceptionHandler, SuccessResponse;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLoginForm()
    {
        // If user is already authenticated, redirect to admin dashboard
        if (Auth::check()) {
            return redirect('/');
        }
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            $user = $this->authService->loginWeb($request->only('email', 'password'));
            // إعادة التوجيه للوحة التحكم بعد تسجيل الدخول
            return redirect()->intended('/');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // إعادة التوجيه مع الأخطاء إلى صفحة تسجيل الدخول (Inertia)
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع');
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request);
            return redirect()->route('login');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'حدث خطأ أثناء تسجيل الخروج');
        }
    }

    public function logoutFromAllDevices(Request $request)
    {
        try {
            $this->authService->logoutFromAllDevices($request->user());
            //return $this->successResponse(null, 'تم تسجيل الخروج من جميع الأجهزة بنجاح');
        } catch (\Exception $e) {
            // ignore
        }
    }

    /*public function me(Request $request)
    {
        return $this->successResponse([
            'user' => $request->user()
        ], 'تم جلب بيانات المستخدم بنجاح');
    }*/
}
