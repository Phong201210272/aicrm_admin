<?php

namespace App\Http\Controllers;

use App\Events\CustomerLogin;
use App\Models\User;
use App\Models\OTP;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function login(Request $request, $username)
    {
        try {
            Log::info('Bắt đầu quá trình đăng nhập', ['username' => $username]);

            // Lấy thông tin đăng nhập từ request
            $credentials = $request->only(['email', 'password']);
            Log::info('Lấy thông tin đăng nhập', ['credentials' => $credentials]);

            // Gọi tới service để xác thực
            $result = $this->userService->authenticateUser($credentials);
            Log::info('Kết quả xác thực từ service', ['result' => $result]);

            // So sánh username
            if ($result['user']['username'] === $username) {
                Log::info('Username hợp lệ', ['username' => $username]);

                // Kiểm tra role_id
                if ($result['user']->role_id == 1) {
                    session()->put('authUser', $result['user']);
                    // session()->put('userName', Auth::user()->username);
                    session()->save();
                    Log::info('Đăng nhập thành công với role admin', ['username' => $username]);
                    return redirect()->route('admin.{username}.dashboard', ['username' => $username]);
                } elseif ($result['user']->role_id == 2) {
                    session()->put('authUser', $result['user']);
                    session()->save();
                    Log::info('Đăng nhập thành công với role staff', ['username' => $username]);
                    return redirect()->route('staff.index');
                } elseif ($result['user']->role_id == 3) {
                    session()->put('authUser', $result['user']);
                    session()->save();
                    Log::info('Đăng nhập thành công với role sa', ['username' => $username]);
                    return redirect()->route('sa.store.index');
                }
            }

            Log::warning('Username không khớp', ['username' => $username, 'resultUsername' => $result['user']['username']]);
            return back();
        } catch (\Exception $e) {
            Log::error('Lỗi trong quá trình đăng nhập', ['error' => $e->getMessage()]);
            return $this->handleLoginError($request, $e);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        return redirect()->route('default');
    }

    protected function handleLoginError($request, \Exception $e)
    {
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
}
