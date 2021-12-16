<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'email'  => 'required|email|unique:users,email',
            'password' => 'required|string',
            'passwordConfirmation' => 'required|same:password',
        ]);

        $users = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'Registration success'
        ], 202);
    }

    public function login(Request $request) {
        $creds = $request->only('email', 'password');

        if(!$token = auth()->attempt($creds)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me() {
        return response()->json(auth()->user());
    }

    public function logout(Request $request) {
        auth()->logout();
        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }

    public function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()*60
        ]);
    }
}
