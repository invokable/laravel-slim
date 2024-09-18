<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules;

/**
 * This is a sample authentication for the stateless API. Feel free to modify it.
 */

Route::post('register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->string('password')),
    ]);

    $token = $user->createToken('api')->plainTextToken;

    event(new Registered($user));

    return ['status' => 'register', 'token' => $token];
});

Route::post('login', function (Request $request) {
    if (Auth::attempt($request->only('email', 'password'))) {
        $token = Auth::user()->createToken('api')->plainTextToken;

        return ['status' => 'login', 'token' => $token];
    }

    return response()->json(['status' => 'error', 'error' => __('auth.failed')], 401);
});

Route::middleware('auth:sanctum')
    ->delete('logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'logout']);
    });
