<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // Authentication passed...
            // return redirect()->intended('dashboard');
            $user = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('Auth Token')->accessToken;
            return response()->json($token, 200);
            // return response()->json(['token' => $token, 'tokenId' => $user->token()->id], 200);
            // return response()->json('Login SUCCESS', 200);
        } else {
            return response()->json('Login FAIL', 403);
        }
        // $user = User::whereUsername($request->username)->findOrFail();
        // if (!Hash::check($request->password, $user->password)) abort(403);
        // $token = $user->createToken('Token Name')->accessToken;
        // return response()->json(['token' => $token], 200);
    }

    public function logout(Request $request)
    {
        // $request->user()->tokens()->delete();
        $tokenId = $request->user()->token()->id;
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        // Revoke an access token...
        $tokenRepository->revokeAccessToken($tokenId);

        // Revoke all of the token's refresh tokens...
        // $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

        return response()->json('Revoke Token Successful', 200);
    }
}
