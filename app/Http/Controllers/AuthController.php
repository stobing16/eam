<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        if (Auth::attempt($data)) {
            $user = Auth::user();
            $token = $user->createToken('eam')->plainTextToken;

            return response()->json([
                "success" => true,
                "message" => "Login Success",
                "token" => $token
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // public function refresh(Request $request): JsonResponse
    // {
    //     $user = Auth::user();
    //     $request->user()->tokens()->delete();
    //     $tokens = $this->service->generateTokens($user);

    //     return $this->sendResponseWithTokens($tokens);
    // }

    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $id = User::getNextRowId();

        try {
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!'
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
