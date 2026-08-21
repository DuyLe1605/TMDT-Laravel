<?php

namespace App\Http\Controllers;

use App\Constants\AppConstants;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Hiển thị form đăng nhập.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập người dùng.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            Log::info('User logged in: ' . Auth::user()->email);

            // Admin → redirect về admin dashboard lần đầu
            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_LOGIN_SUCCESS);
            }

            // Customer → redirect về trang chủ
            return redirect()->intended(route('home'))
                ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_LOGIN_SUCCESS);
        }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'Địa chỉ email hoặc mật khẩu không chính xác.',
            ])
            ->with(AppConstants::FLASH_ERROR, AppConstants::MSG_LOGIN_FAILED);
    }

    /**
     * Hiển thị form đăng ký.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký người dùng mới.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        try {
            Log::info('Registering user with email: ' . $request->email);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Auto-hashed via casts
                'role' => 'customer',
            ]);

            Log::info('User registered successfully: ' . $request->email);

            return redirect()->route('login')
                ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_REGISTER_SUCCESS);
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());

            return redirect()->back()
                ->withInput($request->only('name', 'email'))
                ->with(AppConstants::FLASH_ERROR, AppConstants::MSG_REGISTER_FAILED);
        }
    }

    /**
     * Xử lý đăng xuất người dùng.
     */
    public function logout(Request $request): RedirectResponse
    {
        Log::info('User logged out: ' . Auth::user()?->email);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_LOGOUT_SUCCESS);
    }
}
