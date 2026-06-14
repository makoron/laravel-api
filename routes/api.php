<?php

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// 記事一覧（公開用: 認証不要）
Route::get('/articles', function () {
    return Article::query()
        ->where('is_published', true)
        ->orderBy('published_at', 'desc')
        ->get();
});

// 記事詳細（公開用: 認証不要）
Route::get('/articles/{id}', function ($id) {
    return Article::query()
        ->where('is_published', true)
        ->findOrFail($id);
});

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

    return response()->json([
        'token' => $user->createToken('api-token')->plainTextToken,
    ]);
});

// 管理者ログイン確認用エンドポイント
Route::middleware('auth:sanctum')->get('/admin/data', function (Request $request) {
    return response()->json([
        'message' => 'You are authenticated as admin: ' . $request->user()->email,
    ]);
});


// 管理者用（認証必須）
Route::middleware('auth:sanctum')->group(function () {
    // 記事一覧（管理UI用: 認証必須）
    Route::get('/admin/articles', function () {
        return Article::orderBy('created_at', 'desc')->get();
    });

    // 記事新規作成
    Route::post('/admin/articles', function (Request $request) {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|string|max:255',
            'image_alt' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean'
        ]);

        $article = Article::create($data);

        return response()->json($article, 201);
    });

    Route::get('/admin/articles/{id}', function ($id) {
        return Article::findOrFail($id);
    });

    // 記事更新
    Route::put('/admin/articles/{id}', function (Request $request, $id) {
        $article = Article::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'image' => 'nullable|string|max:255',
            'image_alt' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean'
        ]);

        $article->update($data);

        return response()->json($article);
    });

    // 記事削除
    Route::delete('/admin/articles/{id}', function ($id) {
        $article = Article::findOrFail($id);
        $article->delete();

        return response()->json(['message' => 'Deleted']);
    });

    Route::post('/admin/upload-image', function (Request $request) {
    $request->validate([
        'image' => 'required|image|max:2048',
    ]);

    $path = $request->file('image')->store('articles', 'public');

    return response()->json([
        'url' => asset('storage/' . $path),
        'path' => $path,
    ]);
});
});
