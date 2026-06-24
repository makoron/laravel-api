<?php

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Region;
use App\Models\Prefecture;
use App\Models\Area;

// 記事一覧（公開用: 認証不要）
Route::get('/articles', function () {
    return Article::with(['region', 'prefecture', 'area'])
        ->where('is_published', true)
        ->orderBy('display_order')
        ->orderByDesc('published_at')
        ->get();
});

// 記事詳細（公開用: 認証不要）
// slug があれば slug、数字なら id でも取得できる
Route::get('/articles/{slugOrId}', function ($slugOrId) {
    $article = Article::with(['region', 'prefecture', 'area'])
        ->where('is_published', true)
        ->where(function ($query) use ($slugOrId) {
            $query->where('slug', $slugOrId);

            if (ctype_digit((string) $slugOrId)) {
                $query->orWhere('id', $slugOrId);
            }
        })
        ->firstOrFail();

    $relatedArticles = Article::with(['region', 'prefecture', 'area'])
        ->where('is_published', true)
        ->where('id', '!=', $article->id)
        ->when($article->area_id, function ($query) use ($article) {
            $query->where('area_id', $article->area_id);
        }, function ($query) use ($article) {
            $query->where('prefecture_id', $article->prefecture_id);
        })
        ->orderBy('display_order')
        ->orderByDesc('published_at')
        ->limit(4)
        ->get();

    return response()->json([
        'article' => $article,
        'related_articles' => $relatedArticles,
    ]);
});

Route::get('/regions', function () {
    return Region::orderBy('sort_order')->get();
});

Route::get('/prefectures', function () {
    return Prefecture::orderBy('sort_order')->get();
});

Route::get('/areas', function () {
    return Area::orderBy('sort_order')->get();
});

Route::get('/regions/{id}/prefectures', function ($id) {
    return Prefecture::where('region_id', $id)
        ->orderBy('sort_order')
        ->get();
});

Route::get('/prefectures/{id}/areas', function ($id) {
    return Area::where('prefecture_id', $id)
        ->orderBy('sort_order')
        ->get();
});

Route::get('/prefectures/{slug}/articles', function ($slug) {
    $prefecture = Prefecture::with(['region', 'areas'])
        ->where('slug', $slug)
        ->firstOrFail();

    $articles = Article::with(['region', 'prefecture', 'area'])
        ->where('is_published', true)
        ->where('prefecture_id', $prefecture->id)
        ->orderBy('display_order')
        ->orderByDesc('published_at')
        ->get();

    return response()->json([
        'prefecture' => $prefecture,
        'areas' => $prefecture->areas,
        'articles' => $articles,
    ]);
});

Route::get('/regions/by-slug/{slug}/prefectures', function ($slug) {
    $region = Region::where('slug', $slug)->firstOrFail();

    $prefectures = Prefecture::where('region_id', $region->id)
        ->orderBy('sort_order')
        ->get();

    return response()->json([
        'region' => $region,
        'prefectures' => $prefectures,
    ]);
});

Route::get('/areas/{slug}/articles', function ($slug) {
    $area = Area::with('prefecture.region')
        ->where('slug', $slug)
        ->firstOrFail();

    $articles = Article::with(['region', 'prefecture', 'area'])
        ->where('is_published', true)
        ->where('area_id', $area->id)
        ->orderBy('display_order')
        ->orderByDesc('published_at')
        ->get();

    return response()->json([
        'area' => $area,
        'articles' => $articles,
    ]);
});

Route::get('/search', function (Request $request) {
    $keyword = trim((string) $request->query('q', ''));

    if ($keyword === '') {
        return response()->json([]);
    }

    $articles = Article::with(['region', 'prefecture', 'area'])
        ->where('is_published', true)
        ->where(function ($query) use ($keyword) {
            $query->where('title', 'like', '%' . $keyword . '%')
                ->orWhere('body', 'like', '%' . $keyword . '%')
                ->orWhere('body_html', 'like', '%' . $keyword . '%');
        })
        ->orderBy('display_order')
        ->orderByDesc('published_at')
        ->get();

    return response()->json($articles);
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
        return Article::with(['region', 'prefecture', 'area'])
            ->orderByDesc('is_featured')
            ->orderBy('display_order')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();
    });

    // 記事新規作成
    Route::post('/admin/articles', function (Request $request) {
        $data = $request->validate([
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'body_html' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'image_alt' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'region_id' => 'nullable|exists:regions,id',
            'prefecture_id' => 'nullable|exists:prefectures,id',
            'area_id' => 'nullable|exists:areas,id',
            'display_order' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        $article = Article::create($data);

        return response()->json($article, 201);
    });

    Route::get('/admin/articles/{id}', function ($id) {
        return Article::with(['region', 'prefecture', 'area'])->findOrFail($id);
    });

    // 記事更新
    Route::put('/admin/articles/{id}', function (Request $request, $id) {
        $article = Article::findOrFail($id);

        $data = $request->validate([
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'body_html' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'image_alt' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'region_id' => 'nullable|exists:regions,id',
            'prefecture_id' => 'nullable|exists:prefectures,id',
            'area_id' => 'nullable|exists:areas,id',
            'display_order' => 'nullable|integer',
            'is_featured' => 'boolean',
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
