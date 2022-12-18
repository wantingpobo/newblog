<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct()
    {
        // $this指的是AuthController
        //除了login以外的方法必須通過api版本的auth中介層(middleware)->會觸發JWT的驗證機制
        $this->middleware('auth:api')->except('login');
    }

    public function login()
    {
        //用陣列給資料

        $credentials = request(['email', 'password']);

        try {
            if (!$token = auth()->guard('api')->attempt($credentials)) {
                return response()->json(['status' => 0, 'message' => '無效的驗證資料'], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => '無法建立 Token',
            ], 500);
        }

        return response()->json(['status' => 1, 'token' => $token]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['status' => 1]);
    }
}