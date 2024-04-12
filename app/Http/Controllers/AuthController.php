<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->validated();
        $credential = $request->only(['email', 'password']);
        if (auth()->attempt($credential)) {
            $user = User::find(auth()->user()->id);
            $token = $user->createToken('library');
            return response()->json([
                'token' => $token->plainTextToken,
                'message' => 'Login successfully'
            ], 200);
        } else {
            return response()->json(['User name and password does not match.'], 401);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::guard('api')->user();

            if ($user) {
                PersonalAccessToken::where('tokenable_id', $user->id)->delete(); // Revoke a specific token
            }
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error logging out'], 500);
        }
    }
}
