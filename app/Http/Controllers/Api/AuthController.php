<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function token(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = $request->user();
        if (!$user->tenant_id) {
            Auth::logout();
            return response()->json(['message' => 'No workspace associated'], 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json(['token' => $token, 'token_type' => 'Bearer']);
    }
}
