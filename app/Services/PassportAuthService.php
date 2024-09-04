<?php

namespace App\Services;


use Illuminate\Support\Facades\Auth;
// use Laravel\Sanctum\PersonalAccessToken; 
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\RefreshToken;
use App\Models\User;

class PassportAuthService implements AuthServiceInterface
{
    public function login(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $token = $user->createToken('API Token')->accessToken;
            // return ['user' => $user, 'token' => $token];
            // dd($credentials, "passport");
            return  response()->json(['token' => $token]);
            
        }
        return response()->json(['error ' => 'Invalid credentials'], 404);
    }

    public function logout()
    {
        // $user =Auth::user();
        // $user->tokens()->delete();

        // RefreshToken::where('user_id', $user->id)->delete();

        // return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
    }
}
