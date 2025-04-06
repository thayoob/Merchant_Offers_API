<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponseResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return ApiResponseResource::failureResponse('Validation failed', 422, $validator->errors());
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            $token = $user->createToken('AccessToken')->accessToken;

            return ApiResponseResource::successResponse(
                ApiResponseResource::ACTION_INSERT,
                ApiResponseResource::$User,
                $user->id,
                201,
                ['token' => $token, 'user' => $user]
            );
        } catch (\Exception $e) {
            return ApiResponseResource::failureResponse('Something went wrong during registration.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return ApiResponseResource::failureResponse('Unauthorized', 401);
            }

            $user = Auth::user();
            $token = $user->createToken('AccessToken')->accessToken;

            return ApiResponseResource::successResponse(
                ApiResponseResource::ACTION_FETCH,
                ApiResponseResource::$User,
                $user->id,
                200,
                ['token' => $token, 'user' => $user]
            );
        } catch (\Exception $e) {
            return ApiResponseResource::failureResponse('Login failed.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->token()->revoke();

            return ApiResponseResource::successResponse(
                ApiResponseResource::ACTION_DELETE,
                ApiResponseResource::$User,
                $request->user()->id,
                200,
                null,
                'User logged out successfully.'
            );
        } catch (\Exception $e) {
            return ApiResponseResource::failureResponse('Logout failed.', 500, ['error' => $e->getMessage()]);
        }
    }

    public function verifyToken(Request $request)
    {
        try {
            $user = $request->user();
            return ApiResponseResource::successResponse(
                ApiResponseResource::ACTION_FETCH,
                ApiResponseResource::$User,
                $user->id,
                200,
                ['user' => $user],
                'Token is valid.'
            );
        } catch (\Exception $e) {
            return ApiResponseResource::failureResponse('Invalid token.', 401, ['error' => $e->getMessage()]);
        }
    }
}
