<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        
        return response()->json([
            'userId' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token,
        ],201);
    }

    public function login(Request $request)
    {   
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);
        // $credentials = $request->only('email', 'password');
        // if (!Auth::attempt($credentials)) {
        //     return response()->json(['message' => 'Invalid login details'], 401);
        // }
        // $user = Auth::user();

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ],201);
    }
    public function logout(){
        $user = User::findOrFail(auth()->id());
        $user->tokens()->delete();

        return response()->json([
            'message' => 'token successfully deleted'
        ], 204);
    }
}
