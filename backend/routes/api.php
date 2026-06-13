<?php

use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\InteractionController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\SpecsController;
use App\Http\Controllers\Api\V1\BookmarkController;
use Illuminate\Support\Facades\Route;

Route::prefix('techbyte')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);
    // TODO: Enable forgot-password after the full reset flow is defined.
    // Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);
    Route::get('home', [\App\Http\Controllers\Api\V1\HomeController::class, 'index']);
    Route::get('articles/{id}/specs', [SpecsController::class, 'show']);
    Route::get('articles/{id}/comments', [CommentController::class, 'indexByArticle']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::middleware('api.role:user,admin')->group(function (): void {
            Route::post('comments', [CommentController::class, 'store']);
            Route::post('articles/{id}/like', [InteractionController::class, 'like']);
            Route::post('articles/{id}/bookmark', [BookmarkController::class, 'bookmark']);
            Route::get('me/bookmarks/count', [BookmarkController::class, 'count']);
            Route::get('me/bookmarks', [BookmarkController::class, 'index']);
            Route::get('me', [MeController::class, 'me']);
        });
    });
});
