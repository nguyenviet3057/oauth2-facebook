<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToFacebook() {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback() {
        $user = Socialite::driver('facebook')->user();

        // Kiểm tra xem người dùng đã tồn tại trong hệ thống hay chưa
        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            // Đăng nhập người dùng tồn tại
            $token = $existingUser->createToken('AppName')->accessToken;
        } else {
            // Tạo người dùng mới và đăng nhập
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => bcrypt(Str::random(16)), // Password tạm thời
            ]);

            $token = $newUser->createToken('AppName')->accessToken;
        }

        // Trả về token xác thực cho người dùng
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
