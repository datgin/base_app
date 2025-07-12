<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

// use Illuminate\Routing\Controllers\HasMiddleware;
// use Illuminate\Routing\Controllers\Middleware;

class AuthController extends Controller
{

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->password = $request->password;
        $user->save();

        return response()->json($user, 201);
    }


    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|max:150',
            'password' => 'required',
        ]);

        if (!$token = JWTAuth::attempt($credentials)) {
            return errorResponse(message: "Tài khoản hoặc mật khẩu không chính xác!", code: 400);
        }

        return successResponse("Đăng nhập thành công.", [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            // Lấy thông tin người dùng từ token
            $user = JWTAuth::parseToken()->authenticate();

            return successResponse("Lấy thông tin tài khoản thành công.", $user);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Token is invalid or expired:' . $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        try {
            // Lấy token mới
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            $expiresIn = config('jwt.ttl') * 60;

            return response()->json([
                'status' => 'success',
                'token' => $newToken,
                'expires_in' => $expiresIn, // Thời gian hết hạn tính bằng giây
            ], 200);
        } catch (JWTException $e) {
            logger($e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid or expired',
            ], 401);
        }
    }
}
