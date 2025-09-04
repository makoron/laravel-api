<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// 管理者ログイン → APIトークン発行
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    // Sanctum トークン発行
    return [
        'token' => $user->createToken('api-token')->plainTextToken,
    ];
});

// 認証が必要なルート例
Route::middleware('auth:sanctum')->get('/admin/data', function (Request $request) {
    return ['message' => 'You are authenticated as ' . $request->user()->email];
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin/data', function (Request $request) {
        if ($request->user()->is_admin) {
            return response()->json([
                'message' => "You are authenticated as admin: {$request->user()->email}"
            ]);
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    });
});
