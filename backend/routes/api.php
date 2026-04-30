<?php

use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\InteractionController;
use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\SpecsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);
    Route::get('articles/{id}/specs', [SpecsController::class, 'show']);
    Route::get('articles/{id}/comments', [CommentController::class, 'indexByArticle']);

    Route::middleware('api.token')->group(function (): void {
        Route::middleware('api.role:user,admin')->group(function (): void {
            Route::post('comments', [CommentController::class, 'store']);
            Route::post('articles/{id}/like', [InteractionController::class, 'like']);
            Route::post('articles/{id}/bookmark', [InteractionController::class, 'bookmark']);
            Route::get('me', [MeController::class, 'me']);
        });
    });
});
